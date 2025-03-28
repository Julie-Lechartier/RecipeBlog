<?php

namespace App\Controller;

use App\Repository\RecipeCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\RecipeRepository;

#[Route('/include')]
final class IncludeController extends AbstractController
{

    #[Route('/recipe/scroll', name: 'app_include_recipe_scroll', methods: ['GET'])]
    public function recipeScroll(RecipeRepository $recipeRepository, Request $request): Response
    {
        $limit = $request->query->get('limit', 10);
        $recipes = $recipeRepository->findBy([], null, $limit);
        return $this->render('include/_recipeScrollCard.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recipe/category/{category}', name: 'app_include_recipe_category', methods: ['GET'])]
    public function recipeCategory(RecipeRepository $recipeRepository, RecipeCategoryRepository $categoryRepository, $category): Response
    {
        // Fetch the category entity from the repository by its name
        $categoryEntity = $categoryRepository->findOneBy(['name' => $category]);

        // Only get recipes that belong to the current category
        $recipes = $categoryEntity ? $recipeRepository->findByCategory($categoryEntity) : [];

        return $this->render('include/_recipeCard.html.twig', [
            'recipes' => $recipes,
            'currentCategory' => $categoryEntity,
            'currentCategoryId' => $categoryEntity ? $categoryEntity->getId() : null
        ]);
    }

}
