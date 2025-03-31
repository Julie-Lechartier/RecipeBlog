<?php
namespace App\Controller\Admin;

use App\Entity\RecipeCategory;
use App\Form\RecipeCategoryType;
use App\Repository\RecipeCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/category')]
class RecipeCategoryController extends AbstractController
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

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
            $recipeCategory->setSlug($this->slugger->slug($recipeCategory->getName()));
            $entityManager->persist($recipeCategory);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_recipe_category_index');
        }
        return $this->render('admin/category/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/edit/{slug}', name: 'app_admin_recipe_category_edit', methods: ['GET', 'POST'])]
    public function edit(RecipeCategory $recipeCategory, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeCategoryType::class, $recipeCategory);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $recipeCategory = $form->getData();
            $entityManager->persist($recipeCategory);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_recipe_category_index');
        }
        return $this->render('admin/category/edit.html.twig', [
            'form' => $form->createView(),
            'recipeCategory' => $recipeCategory
        ]);
    }

    #[Route('/delete/{slug}', name: 'app_admin_recipe_category_delete', methods: ['POST'])]
    public function delete(Request $request, RecipeCategory $recipeCategory, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si la catégorie est utilisée dans des recettes
        if ($recipeCategory->getRecipes()->count() > 0) {
            $this->addFlash('error', 'Impossible de supprimer cette catégorie car elle est utilisée dans des recettes.');
            return $this->redirectToRoute('app_admin_recipe_category_index');
        }

        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('delete'.$recipeCategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($recipeCategory);
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie a été supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Token de sécurité invalide.');
        }

        return $this->redirectToRoute('app_admin_recipe_category_index');
    }
}