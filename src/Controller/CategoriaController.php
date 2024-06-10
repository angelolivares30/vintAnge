<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Form\CategoriaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\CategoriaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoriaController extends AbstractController
{
    private $categoriaRepository;

    public function __construct(CategoriaRepository $categoriaRepository)
    {
        $this->categoriaRepository = $categoriaRepository;
    }

    public function obtenerTodasLasCategorias(): array
    {
        return $this->categoriaRepository->findAll();
    }

    #[Route('/verCategorias', name: 'ver_categorias')]
    public function verCategorias(CategoriaRepository $categoriaRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Acceso denegado');
        // Obtener todas las incidencias del repositorio
        $categorias = $categoriaRepository->findAll();
        return $this->render('categoria/verCategorias.html.twig', [
            'categorias' => $categorias,
        ]);
    }


    #[Route('/insertarCategoria', name: 'add_categoria')]
    public function index(Request $request, EntityManagerInterface $em, CategoriaRepository $categoriaRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Acceso denegado');
        $categorias = $categoriaRepository->findAll();
        $categoria = new Categoria();
        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('ss', '¡Se ha añadido correctamente!');

            $em->persist($categoria);
            $em->flush();

            return $this->redirectToRoute('ver_categorias'); // Redirigir a la lista de clientes después de enviar el formulario
        }

        return $this->render('categoria/index.html.twig', [
            'form' => $form->createView(),
            'categorias' => $categorias
        ]);
    }

    #[Route('/categoria/delete/{id}', name:'deleteCategoria')]
    public function delete(Categoria $categoria, EntityManagerInterface $entityManager):Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Acceso denegado');
        $this->addFlash('ss', '¡Se ha borrado correctamente!');

        $entityManager->remove($categoria);
        $entityManager->flush();
        return $this->redirectToRoute('ver_categorias');
    }
}
