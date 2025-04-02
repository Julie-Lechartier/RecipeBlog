<?php

namespace App\Controller;

use App\Repository\RecipeCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NavigationController extends AbstractController
{
    #[Route('/nav', name: 'app_navigation')]
    public function header(RecipeCategoryRepository $recipeCategoryRepository): Response
    {

        $category = $recipeCategoryRepository->findAll();
        return $this->render('components/header.html.twig', [
            'category' => $category,
        ]);
    }
}
