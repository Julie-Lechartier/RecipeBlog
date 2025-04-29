<?php
namespace App\Controller;

use App\Repository\BannerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController{

    #[Route('/', name: 'app_home')]
    public function index(BannerRepository $bannerRepository) : Response
    {
        $banner  = $bannerRepository->findOneBy(['isActive' => true]);
        return $this->render('index.html.twig', [
            'banner' => $banner
        ]);
    }
}
