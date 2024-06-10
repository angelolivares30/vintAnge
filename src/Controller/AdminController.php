<?php

namespace App\Controller;

use App\Repository\CategoriaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'vistaAdmin')]
    public function admin(CategoriaRepository $categoriaRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Acceso denegado');
        $categorias = $categoriaRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'categorias' => $categorias
        ]);
    }
}
