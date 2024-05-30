<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Form\ProductoType;
use App\Repository\CategoriaRepository;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductoController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'listarProductos')]
    public function listarProductos( ProductoRepository $productoRepository): Response
    {
        $productos = $productoRepository->findAll();
        return $this->render('producto/index.html.twig', [
            'productos' => $productos,
        ]);
    }
//-----------------------INSERTAR PRODUCTO---------------------------//

    #[Route('/insertarProducto', name: 'addProducto')]
    public function addProducto(Request $request, SluggerInterface $slugger, CategoriaRepository $categoriaRepository): Response
    {
    $this->denyAccessUnlessGranted('ROLE_USER', null, 'Acceso denegado');
    $producto = new Producto();
    $categorias = $categoriaRepository->findAll();
    $categoriaChoices = [];
        foreach ($categorias as $categoria) {
            $categoriaChoices[$categoria->getNombre()] = $categoria;
        }
    $form = $this->createForm(ProductoType::class, $producto, [
        'categorias' => $categoriaChoices
    ]);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $file = $form->get('foto')->getData();
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename .'-'. uniqid().'.'. $file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                throw new \Exception('error, ha habido un error con tu imagen');
            }
            $producto->setFoto($newFilename);
        }


        $this->em->persist($producto);
        $this->em->flush();
        $this->addFlash('ss', '¡Se ha añadido correctamente!');
        
        return $this->redirectToRoute('listarProductos'); // Redirigir a la lista de productos después de enviar el formulario
    }
    
    return $this->render('producto/crearProducto.html.twig', [
        'formProducto' => $form->createView()
    ]);
    }

//-------------------------------BORRAR PRODUCTO----------------//

    #[Route('/producto/delete/{id}', name:'deleteProducto')]
    public function delete(Producto $producto, EntityManagerInterface $entityManager):Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Acceso denegado');
        $this->addFlash('ss', '¡Se ha borrado correctamente!');

        $entityManager->remove($producto);
        $entityManager->flush();
        return $this->redirectToRoute('listarProductos');
    }

//---------------------EDITAR PRODUCTO-----------------------//

#[Route('/editarProducto/{id}', name: 'editProducto')]
public function editProducto(int $id, Request $request, SluggerInterface $slugger, CategoriaRepository $categoriaRepository, ProductoRepository $productoRepository): Response
{
    $this->denyAccessUnlessGranted('ROLE_USER', null, 'Acceso denegado');

    $producto = $productoRepository->find($id);
    if (!$producto) {
        throw $this->createNotFoundException('El producto no existe');
    }

    $categorias = $categoriaRepository->findAll();
    $categoriaChoices = [];
    foreach ($categorias as $categoria) {
        $categoriaChoices[$categoria->getNombre()] = $categoria;
    }

    $form = $this->createForm(ProductoType::class, $producto, [
        'categorias' => $categoriaChoices
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $file = $form->get('foto')->getData();
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename .'-'. uniqid().'.'. $file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                throw new \Exception('error, ha habido un error con tu imagen');
            }
            $producto->setFoto($newFilename);
        }

        $this->em->persist($producto);
        $this->em->flush();
        $this->addFlash('ss', '¡El producto ha sido actualizado correctamente!');
        
        return $this->redirectToRoute('listarProductos'); // Redirigir a la lista de productos después de enviar el formulario
    }

    return $this->render('producto/editarProducto.html.twig', [
        'formProducto' => $form->createView(),
        'producto' => $producto
    ]);
}


}
