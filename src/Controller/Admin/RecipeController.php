<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;


#[Route('admin/recipe')]
class RecipeController extends AbstractController
{

    #[Route('/', name: 'app_admin_recipe_index')]
    public function index()
    {
        return $this->render('admin/recipe/index.html.twig');
    }

    #[Route('/new', name: 'app_admin_recipe_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        //add user to a recipe
        $recipe = new Recipe();
        $user = $this->getUser();
        $recipe->setAuthor($user);

        // create form
        $form = $this->createForm(RecipeType::class, $recipe, [
            'author_username' => $user->getUsername(),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $entityManager->persist($recipe);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_user_index');
        }
        return $this->render('admin/recipe/new.html.twig', [
            "form" => $form->createView()
        ]);
    }


}
