<?php

namespace App\Controller;

use App\Repository\Gite\GiteRepository;
use App\Repository\ServicesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/')]
final class HomeController extends AbstractController
{
    public function __construct(
        private readonly GiteRepository $giteRepository,
        private readonly ServicesRepository $servicesRepository,
    ) {
    }

    #[Route('/', name: 'home_index', methods: ['GET'])]
    public function index(): Response
    {
        $featuredGites = $this->giteRepository->findBy(['onHomepage' => true]);
        $services = $this->servicesRepository->findAll();

        return $this->render('home/index.html.twig', [
            'featuredGites' => $featuredGites,
            'services' => $services,
        ]);
    }
}
