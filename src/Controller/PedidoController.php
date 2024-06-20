<?php

namespace App\Controller;

use App\Entity\Pedido;
use App\Entity\Producto;
use App\Form\PedidoType;
use App\Repository\CategoriaRepository;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PedidoController extends AbstractController
{
    #[Route('/pedido/add/{productoId}', name: 'addPedido')]
    public function addPedido(int $productoId ,Request $request, EntityManagerInterface $entityManager, CategoriaRepository $categoriaRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Acceso denegado');
        $categorias = $categoriaRepository->findAll();

        $producto = $entityManager->getRepository(Producto::class)->find($productoId);
        if (!$producto) {
            throw $this->createNotFoundException('Producto no encontrado');
        }

        $pedido = new Pedido();
        $pedido->setProducto($producto);

        $form = $this->createForm(PedidoType::class, $pedido, [
            'action' => $this->generateUrl('addPedido', ['productoId' => $productoId])
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $usuario = $this->getUser();
            $pedido->setUsuario($usuario);
            $total = $producto->getPrecio() * $pedido->getCantidad();
            $pedido->setTotal($total);
            $entityManager->persist($pedido);
            $entityManager->flush();

            return $this->redirectToRoute(('successPedido'));
        }
        return $this->render('pedido/index.html.twig', [
            'form' => $form->createView(),
            'producto' => $producto,
            'categorias' => $categorias
        ]);
    }

    #[Route('/pedido/success', name: 'successPedido')]
    public function succesPedido(CategoriaRepository $categoriaRepository, ProductoRepository $productoRepository) :Response {
        $categorias = $categoriaRepository->findAll();
        return $this->render('pedido/success.html.twig', [
            'categorias' => $categorias,
        ]);
    }
}
