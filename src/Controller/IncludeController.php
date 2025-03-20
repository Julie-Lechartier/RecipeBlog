<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\RecipeRepository;

#[Route('/include')]
final class IncludeController extends AbstractController
{

    #[Route('/recipe', name:'app_include_recipe', methods: ['GET'])]
    public function recipe(RecipeRepository $recipeRepository, Request $request): Response
    {
        $limit = $request->query->get('limit', 10);
        $recipes = $recipeRepository->findBy([],null, $limit);
        return $this->render('include/_homeRecipeCard.html.twig', [
            'recipes' => $recipes,
        ]);

    }

}
