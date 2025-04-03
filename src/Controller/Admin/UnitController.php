<?php

namespace App\Controller\Admin;

use App\Entity\RecipeCategory;
use App\Entity\Unit;
use App\Form\UnitType;
use App\Repository\UnitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/unit')]
class UnitController extends AbstractController
{
    #[Route('/', name: 'app_admin_unit_index', methods: ['GET'])]
    public function index(UnitRepository $unitRepository, Request $request, PaginatorInterface $paginator, EntityManagerInterface $entityManager): Response
    {
        $queryBuilder = $unitRepository->createQueryBuilder('r')->getQuery();
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        $categories = $entityManager->getRepository(RecipeCategory::class)->findAll();

        return $this->render('admin/unit/index.html.twig', [
            'pagination' => $pagination,
            'categories' => $categories,
        ]);
    }
    #[Route('/new', name: 'app_admin_unit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $unit = new Unit();
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($unit);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_unit_index');
        }
        return $this->render('admin/unit/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{slug}', name: 'app_admin_unit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Unit $unit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_unit_index');
        }
        return $this->render('admin/unit/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{slug}', name: 'app_admin_unit_delete', methods: ['POST','DELETE'])]
    public function delete(Request $request, Unit $unit, EntityManagerInterface $entityManager): Response
    {

        $entityManager->remove($unit);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_unit_index');
    }

}