<?php

namespace App\Controller\Admin;

use App\Controller\AdminController;
use App\Entity\Comment;
use App\Entity\Recipe;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/comment')]
class CommentController extends AbstractController
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    #[Route('/', name: 'app_admin_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $commentRepository->createQueryBuilder('r')->getQuery();
        $pagination = $paginator->paginate
        (
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    #[Route('/new', name: 'app_admin_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $comment = new $comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setSlug($this->slugger->slug($comment->getContent()));
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_comment_index');
        }
        return $this->render('admin/comment/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('add/recipe/{slug}', name: 'app_admin_comment_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, Recipe $recipe): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setSlug($this->slugger->slug($comment->getContent()));
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_recipe_index');

        }
        return $this->render('admin/recipe/newComment.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{slug}', name: 'app_admin_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('admin/comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }
    #[Route('delete/{slug}', name: 'app_admin_comment_delete', methods: ['DELETE'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($comment);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_comment_index');

    }
}