<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/user')]
final class UserController extends AbstractController
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher,
                                private readonly SluggerInterface            $slugger,
    )
    {
    }

    #[Route('/', name: 'app_admin_user_index', methods: ['GET', 'POST'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('admin/user/index.html.twig', [
            'users' => $users,

        ]);
    }
    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User(); // Create a new User instance
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $user->setSlug($this->slugger->slug($user->getFirstname() . ' ' . $user->getLastname()));
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_user_index');
        }
        return $this->render('admin/user/new.html.twig', [
            "form" => $form->createView()
        ]);

    }
    #[Route('/{slug}', name: 'app_admin_user_show', methods: ['GET', 'POST'])]
    public function show(User $user,UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $recipes = $user->getRecipes();
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
            'users' => $users,
            'recipes' => $recipes,
        ]);
    }

    #[Route('/edit/{slug}', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_user_index');
        }
        return $this->render('admin/user/edit.html.twig', [
            "form" => $form->createView(),
            "user" => $user
        ]);
    }

    #[Route('/delete/{slug}', name: 'app_admin_user_delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))){
            $entityManager->remove($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_admin_user_index');
    }
}