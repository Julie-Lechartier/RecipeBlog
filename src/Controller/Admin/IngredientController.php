<?php

namespace App\Controller\Admin;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/ingredient')]
class IngredientController extends AbstractController
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    #[Route('/', name: 'app_admin_ingredient_index', methods: ['GET'])]
    public function index(IngredientRepository $ingredientRepository, PaginatorInterface $paginator, EntityManagerInterface $entityManager, Request $request): Response
    {
        $queryBuilder = $ingredientRepository->createQueryBuilder('r')->getQuery();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        $ingredients = $entityManager->getRepository(Ingredient::class)->findAll();

        return $this->render('admin/ingredient/index.html.twig', [
            'ingredient' => $ingredients,
            'pagination' => $pagination,
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
            $ingredient->setSlug($this->slugger->slug($ingredient->getName()));

            $entityManager->persist($ingredient);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_ingredient_index');
        }
        return $this->render('admin/ingredient/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{slug}', name: 'app_admin_ingredient_delete', methods: ['DELETE'])]
    public function delete(Ingredient $ingredient, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $ingredient->getSlug(), $request->request->get('_token'))) {
            $entityManager->remove($ingredient);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_ingredient_index');
    }

    #[Route('/table/filter', name: 'app_admin_ingredient_table_filter', methods: ['GET', 'POST'])]
    public function filterTable(Request $request, PaginatorInterface $paginator, IngredientRepository $ingredientRepository): Response
    {
        $name = $request->query->get('name');
        $queryBuilder = $ingredientRepository->findByName($name);
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('admin/ingredient/_table.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}