<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Entity\Media;
use App\Form\BannerType;
use App\Repository\BannerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
            $banner = $form->getData();
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // TODO: correction
                $newFileName = md5(uniqid(null, true)) . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('banner_images_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {
                    throw new \Exception("Impossible to upload image.");
                }
                $media = new Media();
                $media->setFileName($newFileName);
                $media->setBanner($banner);
                $entityManager->persist($media);
            }
            $entityManager->persist($banner);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_banner_index');
        }

        return $this->render('admin/banner/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/edit/{slug}', name: 'app_admin_banner_edit', methods: ['GET', 'POST'])]
    public function edit(Banner $banner, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BannerType::class, $banner);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $banner = $form->getData();
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $extension = $imageFile->guessExtension() ?? 'bin';
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array($extension, $allowedExtensions)) {
                    throw new \Exception("Extension de fichier non supportée.");
                }
                $newFileName = md5(uniqid()) . '.' . $extension;
                try {
                    $imageFile->move(
                        $this->getParameter('banner_images_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {
                    throw new \Exception("Impossible to upload image.");
                }
                $media = new Media();
                $media->setFileName($newFileName);
                $media->setBanner($banner);
                $entityManager->persist($media);
            }
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_banner_index');
        }
        return $this->render('admin/banner/edit.html.twig', [
            'banner' => $banner,
            'form' => $form->createView(),
        ]);
    }
}
