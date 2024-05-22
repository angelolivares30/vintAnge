<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Form\ProductoType;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    public function addProducto(Request $request): Response
    {
    $this->denyAccessUnlessGranted('ROLE_USER', null, 'Acceso denegado');

    $producto = new Producto();
    $form = $this->createForm(ProductoType::class, $producto);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $this->addFlash('ss', '¡Se ha añadido correctamente!');

        $this->em->persist($producto);
        $this->em->flush();
        
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

#[Route('/producto/edit/{id}', name: 'editProducto')]
public function editProducto(Request $request, $id): Response
{
    $this->denyAccessUnlessGranted('ROLE_USER', null, 'Acceso denegado');

    $producto = $this->em->getRepository(Producto::class)->find($id);

    if (!$producto) {
        throw $this->createNotFoundException('El producto no existe');
    }

    $form = $this->createForm(ProductoType::class, $producto);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $this->em->flush();
        $this->addFlash('ss', '¡Se ha actualizado correctamente!');
        return $this->redirectToRoute('listarProductos'); // Redirigir a la lista de productos después de actualizar el formulario
    }

    return $this->render('producto/editarProducto.html.twig', [
        'formProducto' => $form->createView(),
        'producto' => $producto
    ]);
}


}
