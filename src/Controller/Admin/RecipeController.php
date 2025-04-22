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


#[Route('admin/recipe')]
class RecipeController extends AbstractController
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
    }
    #[Route('/', name: 'app_admin_recipe_index', methods: ['GET'])]
    public function index(RecipeRepository $recipeRepository, Request $request, PaginatorInterface $paginator, EntityManagerInterface $entityManager): Response
    {
        $queryBuilder = $recipeRepository->createQueryBuilder('r')->getQuery();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        $categories = $entityManager->getRepository(RecipeCategory::class)->findAll();

        return $this->render('admin/recipe/index.html.twig', [
            'pagination' => $pagination,
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'app_admin_recipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recipe = new Recipe();

        // Utiliser l'utilisateur connecté comme auteur
        $currentUser = $this->getUser();
        if (!$currentUser) {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            $this->addFlash('error', 'Vous devez être connecté pour créer une recette');
            return $this->redirectToRoute('app_login');
        }

        // Définir l'utilisateur connecté comme auteur
        $recipe->setAuthor($currentUser);

        // Création du formulaire
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier que le titre n'est pas vide avant de générer le slug
            if (empty($recipe->getTitle())) {
                $this->addFlash('error', 'Le titre est requis pour générer un slug');
                return $this->render('admin/recipe/new.html.twig', [
                    "form" => $form->createView(),
                    'recipe' => $recipe,
                ]);
            }

            // Générer et définir le slug ici avec une valeur par défaut si nécessaire
            $slug = $this->slugger->slug($recipe->getTitle())->lower();

            // Vérification supplémentaire pour s'assurer que le slug n'est pas vide
            if (empty($slug)) {
                $slug = $this->slugger->slug('recette-' . uniqid())->lower();
            }

            $recipe->setSlug($slug);

            // Vérification Debug - à retirer après résolution du problème
            dump('Slug généré: ' . $recipe->getSlug());

            // Le reste du code...
            $stepNumber = 1;
            foreach ($recipe->getSteps() as $step) {
                $step->setStepNumber($stepNumber);
                $stepNumber++;
            }

            // Traitement de l'image...
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Le reste du code pour l'image...
            }

            // Traitement des catégories
            $category = $form->get('category')->getData();
            foreach ($category as $categoryItem) {
                $recipe->addCategory($categoryItem);
            }

            // S'assurer une dernière fois que le slug est défini avant la persistance
            if (empty($recipe->getSlug())) {
                $recipe->setSlug($this->slugger->slug('recette-' . uniqid())->lower());
            }

            // Enregistrement en base de données
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
    #[Route('/edit/{slug}', name: 'app_admin_recipe_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Recipe $recipe): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            
            // Gérer les numéros d'étapes
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
            return $this->redirectToRoute('app_admin_recipe_index');
        }
        return $this->render('admin/recipe/new.html.twig', [
            "form" => $form->createView(),
            'recipe' => $recipe,
        ]);
    }

    #[Route('/delete/{slug}', name: 'app_admin_recipe_delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $recipe->getSlug(), $request->request->get('_token'))) {
            $entityManager->remove($recipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_recipe_index');
    }
    #[Route('/table/filter', name: 'table_filter', methods: ['POST'])]
    public function filter(Request $request, RecipeRepository $recipeRepository, PaginatorInterface $paginator): Response
    {
        $search = $request->request->get('search', '');
        $category = $request->request->get('category', '');

        $queryBuilder = $recipeRepository->createFilteredQueryBuilder($search, $category);

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('admin/recipe/_table.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
