<?php

namespace App\Controller;

use App\Repository\RecipeCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NavigationController extends AbstractController
{
    #[Route('/{slug}', name: 'app_navigation')]
    public function header(RecipeCategoryRepository $recipeCategoryRepository): Response
    {
        $categories = $recipeCategoryRepository->findAll();

        return $this->render('components/header.html.twig', [
            'categories' => $categories,
        ]);
    }
}
