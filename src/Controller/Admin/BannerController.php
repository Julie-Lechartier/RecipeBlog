<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Form\BannerType;
use App\Repository\BannerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/banner')]
class BannerController extends AbstractController
{
    #[Route('/', name: 'app_admin_banner_index', methods: ['GET'])]
    public function index(BannerRepository $bannerRepository): Response
    {
        $banner  = $bannerRepository->findOneBy([]);

        return $this->render('admin/banner/index.html.twig',[
            'banner' => $banner
        ]);
    }

    #[Route('/new', name: 'app_admin_banner_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $banner = new Banner();
        $form = $this->createForm(BannerType::class, $banner);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($banner);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_banner_index');
        }

        return $this->render('admin/banner/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
