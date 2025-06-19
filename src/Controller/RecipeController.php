<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Media;
use App\Entity\Recipe;
use App\Entity\RecipeCategory;
use App\Form\RecipeType;
use App\Repository\CommentRepository;
use App\Repository\RecipeIgredientRepository;
use App\Repository\RecipeIngredientRepository;
use App\Repository\RecipeRepository;
use App\Repository\RecipeCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/recipe')]
class RecipeController extends AbstractController
{

    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    #[Route('/', name: 'app_recipe_index')]
    public function index(RecipeRepository $recipeRepository)
    {
        $recipes = $recipeRepository->findAll();
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }
    #[Route('/show/{slug}', name: 'app_recipe_show')]
    public function show(Request $request, RecipeRepository $recipeRepository, RecipeIngredientRepository $recipeIngredientRepository,CommentRepository $commentRepository, string $slug): Response
    {
        $recipe = $recipeRepository->findOneBy(['slug' => $slug]);
        $recipeIngredient = $recipeIngredientRepository->findBy(['recipe' => $recipe]);
        $comments = $commentRepository->findBy(['recipe' => $recipe]);
        //condition preparation time format
        $preparationTime = $recipe->getPreparationTime();
        $hours = (int)$preparationTime?->format('H');
        $minutes = (int)$preparationTime?->format('i');
        $currentPageRoute = $request->query->get('currentPageRoute');

        return $this->render('recipe/show.html.twig', [
            'recipes' => $recipe,
            'recipeIngredients' => $recipeIngredient,
            'preparationHours' => $hours,
            'preparationMinutes' => $minutes,
            'currentPageRoute' => $currentPageRoute,
            'comments' => $comments,
        ]);
    }
    #[Route('/category/{category}', name: 'app_recipe_category_show')]
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
    #[Route('/new', name: 'app_new_recipe', methods: ['GET', 'POST'])]
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
            // Title not null
            if (empty($recipe->getTitle())) {
                $this->addFlash('error', 'Le titre est requis pour générer un slug');
                return $this->render('admin/recipe/new.html.twig', [
                    "form" => $form->createView(),
                    'recipe' => $recipe,
                ]);
            }

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
    #[Route('/edit/{slug}', name: 'app_recipe_edit')]
    public function edit(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();

            // Steps num
            $stepNumber = 1;
            foreach ($recipe->getSteps() as $step) {
                $step->setStepNumber($stepNumber);
                $stepNumber++;
            }

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFileName = md5(uniqid(null, true)) . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('recipe_images_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {
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
            return $this->redirectToRoute('app_recipe_index');
        }
        return $this->render('recipe/new.html.twig', [
            "form" => $form->createView(),
            'recipe' => $recipe,
        ]);
    }
    #[Route('/delete/{slug}', name: 'app_recipe_delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $recipe->getSlug(), $request->request->get('_token'))) {
            $entityManager->remove($recipe);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_recipe_index');
    }
}