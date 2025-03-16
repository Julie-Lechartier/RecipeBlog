<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
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
                                private readonly SluggerInterface $slugger,
                                private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'app_admin_user_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->add('firstname', TextColumn::class, [
            'label' => 'Prénom'
        ])
        ->add('lastname', TextColumn::class, [
            'label' => 'Nom'
        ])
            ->add('username', TextColumn::class, [
                'label' => 'Pseudo'
            ])
        ->add('email', TextColumn::class, [
            'label' => 'Email'
    ])
        ->add('newsletter', BoolColumn::class, [
            'trueValue' => 'Yes',
            'falseValue' => 'No',
            'label' => 'Newsletter'
        ])
            ->createAdapter(ORMAdapter::class, [
                'entity' => User::class,
            ])
        ->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }
        return $this->render('admin/user/index.html.twig', [
            'datatable' => $table,

        ]);
    }
    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class);
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

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'] ) ]
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
}