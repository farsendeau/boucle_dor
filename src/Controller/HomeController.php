<?php

namespace App\Controller;

use App\Repository\Gite\GiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/')]
final class HomeController extends AbstractController
{
    public function __construct(
        private readonly GiteRepository $giteRepository,
    ) {
    }

    #[Route('/', name: 'home_index', methods: ['GET'])]
    public function index(): Response
    {
        $featuredGites = $this->giteRepository->findBy(['onHomepage' => true]);

        return $this->render('home/index.html.twig', [
            'featuredGites' => $featuredGites,
        ]);
    }
}
