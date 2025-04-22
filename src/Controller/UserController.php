<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeCategory;
use App\Form\RecipeType;
use App\Form\UserPresentationType;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
class UserController extends AbstractController
{

    #[Route('/profile', name: 'app_user_profile', methods: ['GET'])]
    public function index(Request $request, Recipe $recipe): Response
    {
        // get the connected user
        $user = $this->getUser();
        //get the user recipes
        $recipes = $user->getRecipes();
//        //condition for the préparation time

        $currentPageRoute = $request->query->get('currentPageRoute');

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'recipes' => $recipes,
            'currentPageRoute' => $currentPageRoute,
        ]);
    }
    #[Route('/profile/presentation', name: 'app_user_profile_presentation', methods: ['GET', 'POST'])]
    public function presentation( Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserPresentationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->render('user/_presentation.html.twig', [
                'user' => $user,
            ]);
        }
        return $this->render('user/_presentation_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
