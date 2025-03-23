<?php
namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/recipe')]
class RecipeController extends AbstractController{

    #[Route('/', name: 'app_recipe_index')]
    public function index() {
        return $this->render('recipe/index.html.twig');
    }

    #[Route('/{slug}', name: 'app_recipe_view')]
    public function show(Recipe $recipe) {

        return $this->render('recipe/view.html.twig', [
            'recipe' => $recipe,
        ]);
    }
}