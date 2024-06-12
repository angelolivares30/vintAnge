<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Entity\Favorito;
use App\Entity\Producto;
use App\Entity\User;
use App\Form\ProductoType;
use App\Repository\CategoriaRepository;
use App\Repository\FavoritoRepository;
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
    public function listarProductos( ProductoRepository $productoRepository, CategoriaRepository $categoriaRepository, FavoritoRepository $favoritoRepository): Response
    {
        $productos = $productoRepository->findAll();
        $categorias = $categoriaRepository->findAll();
        $favoritos = [];
        $user = $this->getUser();
        if ($user) {
            $favoritos = $favoritoRepository->findBy(['idUsuario' => $user->getId()]);
            $favoritos = array_map(fn($favorito) => $favorito->getIdProducto()->getId(), $favoritos);
        }
        return $this->render('producto/index.html.twig', [
            'productos' => $productos,
            'categorias' => $categorias,
            'favoritos' => $favoritos
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
public function editProducto(Request $request, SluggerInterface $slugger, CategoriaRepository $categoriaRepository, Producto $producto): Response
{
    $this->denyAccessUnlessGranted('ROLE_USER', null, 'Acceso denegado');
    
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

        $this->em->flush();
        $this->addFlash('success', '¡Se ha actualizado correctamente!');
        
        return $this->redirectToRoute('listarProductos'); // Redirigir a la lista de productos después de enviar el formulario
    }
    
    return $this->render('producto/editarProducto.html.twig', [
        'formProducto' => $form->createView(),
        'producto' => $producto
    ]);
}

//-------------------------VISTA DE PRODUCTO POR CATEGORIA-----------------------------

#[Route('/productos/categoria/{id}', name: 'productosPorCategoria')]
    public function productosPorCategoria(Categoria $categoria, ProductoRepository $productoRepository, CategoriaRepository $categoriaRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Acceso denegado');
        $categorias = $categoriaRepository->findAll();
        $productos = $productoRepository->findProductoByCategoria($categoria->getId());
        return $this->render('producto/verProductoPorCategoria.html.twig', [
            'categoria' => $categoria,
            'productos' => $productos,
            'categorias' => $categorias,
        ]);
    }


//---------------------------VISTA DE PRODUCTOS FAVORITOS---------------------------//

    #[Route('/favoritos/productos/user/{id}', name: 'productosFavoritos')]
    public function productosFavoritos (CategoriaRepository $categoriaRepository,FavoritoRepository $favoritoRepository ,ProductoRepository $productoRepository, User $user): Response
    {
        $user = $this->getUser();
        $categorias = $categoriaRepository->findAll();
        if (!$user) {
            return $this->redirectToRoute('login'); 
        }

        $favoritos = $favoritoRepository->findProductosFavoritosByUsuario($user->getId());
        $productosFavoritos = array_map(fn($favorito) => $favorito->getIdProducto(), $favoritos);

        return $this->render('producto/favoritos.html.twig', [
            'productos' => $productosFavoritos,
            'categorias' => $categorias
        ]);
    }

}
