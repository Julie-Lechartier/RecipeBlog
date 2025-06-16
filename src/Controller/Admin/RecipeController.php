<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Entity\Recipe;
use App\Entity\RecipeCategory;
use App\Entity\User;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/recipe')]
class RecipeController extends AbstractController
{
    #[Route('/new', name: 'app_admin_recipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recipe = new Recipe();
        $currentUser = $this->getUser();
        if (!$currentUser) {
            $this->addFlash('error', 'Vous devez être connecté pour créer une recette');
            return $this->redirectToRoute('app_login');
        }
        $recipe->setAuthor($currentUser);
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        $recipe->setCreateAt(new \DateTimeImmutable());
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Numérotation des étapes
            $stepNumber = 1;
            foreach ($recipe->getSteps() as $step) {
                $step->setStepNumber($stepNumber);
                $stepNumber++;
            }

            // Categories
            $category = $form->get('category')->getData();
            foreach ($category as $categoryItem) {
                $recipe->addCategory($categoryItem);
            }

            $entityManager->persist($recipe);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'Recette créée avec succès');
                return $this->redirectToRoute('app_admin_recipe_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
            }
        }

        return $this->render('admin/recipe/new.html.twig', [
            "form" => $form->createView(),
            'recipe' => $recipe,
        ]);
    }
}