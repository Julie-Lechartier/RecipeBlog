<?php

namespace App\Controller\Admin;


use App\Entity\RecipeCategory;
use App\Repository\RecipeCategoryRepository;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class MainController extends AbstractController
{
    #[Route('/', name: 'app_admin_index')]
    public function index(UserRepository $userRepository, RecipeRepository $recipeRepository, RecipeCategoryRepository $recipeCategoryRepository): Response
    {
        $recipeCategories = $recipeCategoryRepository->findAll();
        $recipes = $recipeRepository->findAll();
        $users = $userRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'recipes' => $recipes,
            'recipeCategories' => $recipeCategories,
        ]);
    }
}