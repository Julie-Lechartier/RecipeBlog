<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Entity\Recipe;
use App\Entity\User;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
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
    #[Route('/', name: 'app_admin_recipe_index')]
    public function index(RecipeRepository $recipeRepository)
    {
        $recipes = $recipeRepository->findAll();
        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/new', name: 'app_admin_recipe_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        //add user to a recipe
        $recipe = new Recipe();
        // default user
        $userRepository = $entityManager->getRepository(User::class);
        $defaultUser = $userRepository->findOneBy([]);
        if (!$defaultUser) {
            throw new \Exception('Aucun utilisateur trouvé dans la base de données');
        }
        $recipe->setAuthor($defaultUser);

        // create form
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            
            // TODO: change when authentication is implemented
            if (!$recipe->getAuthor()) {
                $recipe->setAuthor($defaultUser);
            }
            $recipe->setSlug($this->slugger->slug($recipe->getTitle()));
            
            // step num
            $stepNumber = 1;
            foreach ($recipe->getSteps() as $step) {
                $step->setStepNumber($stepNumber);
                $stepNumber++;
            }
            
            $imageFile = $form->get('image')->getData();
            
            if ($imageFile) {
                // type MIME
                $mimeType = $imageFile->getMimeType();
                if (!in_array($mimeType, ['image/jpeg', 'image/png'])) {
                    throw new \Exception('Format de fichier non supporté. Utilisez JPG ou PNG.');
                }

                $newFileName = md5(uniqid(null, true)) . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('recipe_images_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {
                    throw new \Exception("Impossible d'uploader l'image: " . $e->getMessage());
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


}
