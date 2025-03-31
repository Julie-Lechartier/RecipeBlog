<?php

namespace App\Controller\Admin;

use App\Controller\AdminController;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/comment')]
class CommentController extends AbstractController
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    #[Route('/', name: 'app_admin_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        $comment = $commentRepository->findAll();
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $comment,
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

    #[Route('/show/{slug}', name: 'app_admin_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('admin/comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }
    #[Route('delete/{slug}', name: 'app_admin_comment_delete', methods: ['DELETE'])]
    public function delete(Request $request, Comment $comment): Response
    {

    }
}