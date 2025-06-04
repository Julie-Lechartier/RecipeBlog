<?php

namespace App\Controller;

use App\Repository\RecipeCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/nav')]
class NavigationController extends AbstractController
{
    #[Route('/header', name: 'app_navigation_header', methods: ['GET'])]
    public function header(RecipeCategoryRepository $recipeCategoryRepository): Response
    {

        $category = $recipeCategoryRepository->findAll();
        return $this->render('components/header.html.twig', [
            'category' => $category,
        ]);
    }
    #[Route('/footer', name: 'app_navigation_footer', methods: ['GET'])]
    public function footer(RecipeCategoryRepository $recipeCategoryRepository): Response
    {
        $categories = $recipeCategoryRepository->findAll();
        return $this->render('components/footer.html.twig', [
            'categories' => $categories,
        ]);
    }
}
