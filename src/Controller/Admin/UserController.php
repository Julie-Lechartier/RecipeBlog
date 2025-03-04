<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private $passwordHasher;
    private $slugger;

    #[Route('/', name: 'app_admin_user_index', methods: ['GET', 'POST'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig');
    }
    #[Route('/new', name: 'admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $user->setSlug($this->slugger->slug($user->getFirstName() . ' ' . $user->getLastName()));
            $entityManager->persist($user);
            $entityManager->flush();
        }
        
    }
}