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
    public function recipeScroll(RecipeRepository $recipeRepository, Request $request, RecipeCategoryRepository $categoryRepository): Response
    {
        $limit = (int) $request->query->get('limit', 10);
        $categoryId = $request->query->get('category');
        $categoryEntity = null;

        if ($categoryId) {
            $categoryEntity = $categoryRepository->find($categoryId);
        }

        if ($categoryEntity) {
            $recipes = $recipeRepository->findByCategory($categoryEntity, $limit);
        } else {
            $recipes = $recipeRepository->findLatest($limit);
        }

        return $this->render('include/_recipeScrollCard.html.twig', [
            'recipes' => $recipes,
        ]);
    }


    #[Route('/recipe/all', name: 'app_include_recipe_all', methods: ['GET'])]
    public function recipeAll(RecipeRepository $recipeRepository): Response
    {
        $recipes = $recipeRepository->findAll();
        return $this->render('include/_recipeCard.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recipe/category/{category}', name: 'app_include_recipe_category', methods: ['GET'])]
    public function recipeCategory(RecipeRepository $recipeRepository, RecipeCategoryRepository $categoryRepository, $category): Response
    {
        $categoryEntity = $categoryRepository->findOneBy(['name' => $category]);

        $recipes = $categoryEntity ? $recipeRepository->findByCategory($categoryEntity) : [];

        return $this->render('include/_recipeCard.html.twig', [
            'recipes' => $recipes,
            'currentCategory' => $categoryEntity,
            'currentCategoryId' => $categoryEntity ? $categoryEntity->getId() : null
        ]);
    }

}
