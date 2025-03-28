<?php
namespace App\Controller\Admin;

use App\Form\RecipeCategoryType;
use App\Repository\RecipeCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/recipeCategory')]
class RecipeCategoryController extends AbstractController
{
    #[Route('/', name:'app_admin_recipe_category_index', methods: ['GET'])]
    public function index(RecipeCategoryRepository $recipeCategoryRepository)
    {
        $categories = $recipeCategoryRepository->findAll();
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories
        ]);
    }
    #[Route('/new', name: 'app_admin_recipe_category_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(RecipeCategoryType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $recipeCategory = $form->getData();
            $entityManager->persist($recipeCategory);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_recipe_category_index');
        }
        return $this->render('admin/category/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}