<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Repository\RecipeCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/recipe')]
class RecipeController extends AbstractController
{

    #[Route('/', name: 'app_recipe_index')]
    public function index(RecipeRepository $recipeRepository)
    {
        $recipes = $recipeRepository->findAll();
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }
    #[Route('/{slug}', name: 'app_recipe_view')]
    public function show(Recipe $recipe): Response
    {
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }
    #[Route('/category/{category}', name: 'app_recipe_category_view')]
    public function showCategories(string $category, RecipeRepository $recipeRepository, RecipeCategoryRepository $categoryRepository): Response
    {
        $categoryEntity = $categoryRepository->findOneBySlug($category);
        
        if (!$categoryEntity) {
            throw $this->createNotFoundException('Category not found');
        }

        $recipes = $recipeRepository->findByCategory($categoryEntity);

        return $this->render('recipe/viewCategory.html.twig', [
            'recipes' => $recipes,
            'category' => $categoryEntity->getName(),
            'currentCategory' => $categoryEntity,
            'currentCategoryId' => $categoryEntity->getId()
        ]);
    }
    #[Route('/new', name: 'app_new_recipe')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        //add user to a recipe
        $recipe = new Recipe();
        $user = $this->getUser();
        $recipe->setAuthor($user);

        // create form
        $form = $this->createForm(RecipeType::class, $recipe,
        //[
        //     'author_username' => $user->getUsername(),
        // ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFileName = md5(uniqid(null, true)).'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('recipe_images_directory'),
                        $newFileName
                    );
                }
                catch (FileException $e) {
                    throw new \Exception("Impossible to upload image.");
                }
                $media = new Media();
                $media->setFileName($newFileName);
                $media->setRecipe($recipe);
                $entityManager->persist($media);
            }
            $category = $form->get('category')->getData();
            foreach ($category as $categoryItem) {
                $recipe->addCategory($categoryItem);
            }
            $entityManager->persist($recipe);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_recipe_index');
        }
        return $this->render('recipe/new.html.twig', [
            "form" => $form->createView(),
            'recipe' => $recipe,
        ]);
    }

}