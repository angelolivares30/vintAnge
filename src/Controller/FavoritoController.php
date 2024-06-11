<?php

namespace App\Controller;

use App\Entity\Favorito;
use App\Entity\Producto;
use App\Entity\User;
use App\Repository\FavoritoRepository;
use App\Repository\ProductoRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FavoritoController extends AbstractController
{
    #[Route('/favorito', name: 'addFavorito', methods:'POST')]
    public function addOrRemoveFavorito(ProductoRepository $productoRepository, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, FavoritoRepository $favoritoRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $idUsuario = $data['idUsuario'];
        $idProducto = $data['idProducto'];

        $user = $entityManager->getRepository(User::class)->find($idUsuario);
        $producto = $entityManager->getRepository(Producto::class)->find($idProducto);

        if (!$user || !$producto) {
            return new JsonResponse(['error' => 'Usuario o Producto no encontrado'], 404);
        }

        $favorito = new Favorito();
        $favorito->setIdUsuario($user);
        $favorito->setIdProducto($producto);

        $entityManager->persist($favorito);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Producto agregado a favoritos']);
    }
    #[Route('/favorito', name: 'deleteFavorito', methods:'DELETE')]
    public function removeFavorito(Request $request, EntityManagerInterface $em, FavoritoRepository $favoritoRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $idUsuario = $data['idUsuario'];
        $idProducto = $data['idProducto'];

        $favorito = $favoritoRepository->findOneBy(['idUsuario' => $idUsuario, 'idProducto' => $idProducto]);

        if (!$favorito) {
            return new JsonResponse(['error' => 'Favorito no encontrado'], 404);
        }

        $em->remove($favorito);
        $em->flush();

        return new JsonResponse(['message' => 'Producto eliminado de favoritos']);
    }
}
