<?php
namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/recipe')]
class RecipeController extends AbstractController{

    #[Route('/', name: 'app_recipe_index')]
    public function index() {
        return $this->render('recipe/index.html.twig');
    }

    #[Route('/{id}', name: 'app_recipe_view')]
    public function show(int $id, RecipeRepository $recipeRepository) {
        $recipe = $recipeRepository->find($id);
        if (!$recipe) {
            throw $this->createNotFoundException("Recette introuvable !");
        }
        $steps = $recipe->getStep();

        return $this->render('recipe/view.html.twig', [
            'recipe' => $recipe,
            'steps' => $steps,
        ]);
    }
}