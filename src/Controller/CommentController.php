<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Form\UserCommentType;
use App\Repository\CommentRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
#[Route('/comment')]
class CommentController extends AbstractController
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
    }
    #[Route('/show/{recipeSlug}', name: 'app_comment_show', methods: ['GET'])]
    public function show(RecipeRepository $recipeRepository, string $recipeSlug): Response
    {
        $recipe = $recipeRepository->findOneBy(['slug' => $recipeSlug]);
        return $this->render('comment/show.html.twig', [
            'comments' => $recipe->getComments(),
            'recipe' => $recipe,
        ]);
    }
    #[Route('/new/recipe/{recipeSlug}', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Comment $comment, EntityManagerInterface $entityManager, string $recipeSlug, RecipeRepository $recipeRepository): Response
    {
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException('You must be logged in to post a comment.');
        }
        $recipe = $recipeRepository->findOneBy(['slug' => $recipeSlug]);
        $comment = new $comment();
        $comment->setRecipe($recipe);
        $comment->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(UserCommentType::class, $comment, [
            'user_username' => $this->getUser()->getUsername(),
        ]);
        if (!$recipe) {
            throw $this->createNotFoundException('Recipe not found');
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setSlug($this->slugger->slug($comment->getContent()));
            $comment->setUser($this->getUser());
            if (!$comment->getUser()) {
                throw new \LogicException('User is null before saving comment.');
            }
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('app_recipe_show', ['slug' => $recipe->getSlug()]);
        }
        return $this->render('comment/new.html.twig', [
            'form' => $form->createView(),
            'recipe' => $recipe,
        ]);
    }
    #[Route('/edit/{id}', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser() || $this->getUser() !== $comment->getUser()) {
            throw $this->createAccessDeniedException('You can only edit your own comments.');
        }


        $form = $this->createForm(UserCommentType::class, $comment, [
            'user_username' => $this->getUser()->getUsername(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setSlug($this->slugger->slug($comment->getContent()));
            $entityManager->persist($comment);
            $entityManager->flush();

            if ($request->headers->has('HX-Request')) {
                return $this->render('comment/_content.html.twig', [
                    'comment' => $comment,
                ]);
            }

            return $this->redirectToRoute('app_recipe_show', [
                'slug' => $comment->getRecipe()->getSlug()
            ]);
        }

        if ($request->headers->has('HX-Request') && $request->isMethod('GET')) {
            return $this->render('comment/_form-edit.html.twig', [
                'comment' => $comment,
                'form' => $form->createView(),
            ]);
        }


        return $this->render('comment/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment,
            'recipe' => $comment->getRecipe(),
        ]);

    }
    #[Route('/delete/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(
        int $id,
        Request $request,
        CommentRepository $commentRepository,
        EntityManagerInterface $entityManager,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        $comment = $commentRepository->find($id);

        if (!$comment) {
            throw $this->createNotFoundException('Commentaire non trouvé.');
        }

        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete-comment-' . $comment->getId(), $submittedToken)) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recipe_show', ['slug' => $comment->getRecipe()->getSlug()]);
    }


    #[Route('edit/{id}/content', name: 'app_comment_content', methods: ['GET'])]
    public function content(Comment $comment): Response
    {
        return $this->render('comment/_content.html.twig', [
            'comment' => $comment
        ]);
    }
}
