<?php

namespace App\Controller;

use App\Repository\CategoriaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index( AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    #[Route('/logout', name: 'logout')]
    public function logout(AuthenticationUtils $authenticationUtils)
    {
    }

    #[Route('/aboutme', name: 'me')]
    public function aboutMe(CategoriaRepository $categoriaRepository) {
        $categorias = $categoriaRepository->findAll();
        return $this->render('login/aboutme.html.twig', [
            'categorias' => $categorias
        ]);
    }
}
