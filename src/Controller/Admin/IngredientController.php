<?php

namespace App\Controller\Admin;

use App\DataFixtures\IngredientFixtures;
use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/ingredient')]
class IngredientController extends AbstractController
{
    #[Route('/', name: 'app_admin_ingredient_index', methods: ['GET'])]
    public function index(IngredientRepository $ingredientRepository)
    {
        $ingredients = $ingredientRepository->findAll();
        return $this->render('admin/ingredient/index.html.twig', [
            'ingredient' => $ingredients,
        ]);

    }
    #[Route('/new', name: 'app_admin_ingredient_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient->setSlug($this->slugger->slug($ingredient->getName()));

            $entityManager->persist($ingredient);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_ingredient_index');
        }
        return $this->render('admin/ingredient/new.html.twig', [
            'form' => $form->createView(),
        ]);

    }
    #[Route('/edit/{slug}', name: 'app_admin_ingredient_edit', methods: ['GET'])]
    public function edit(Ingredient $ingredient, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_ingredient_index');
        }
        return $this->render('admin/ingredient/edit.html.twig', []);
    }

    #[Route('/delete/{slug}', name: 'app_admin_ingredient_delete', methods: ['DELETE'])]
    public function delete(Ingredient $ingredient, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($ingredient);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_ingredient_index');
    }
}