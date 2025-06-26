<?php

namespace App\Controller;

use App\Repository\Gite\GiteRepository;
use App\Repository\ServicesRepository;
use IntlDateFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(Request $request): Response
    {
        $featuredGites = $this->giteRepository->findBy(['onHomepage' => true]);
        $services = $this->servicesRepository->findAll();
        $nextAvailabilitySlot = $this->giteRepository->findNextAvailableWeekSlot();

        $formattedDate = null;
        if ($nextAvailabilitySlot) {
            $locale = $request->getLocale();
            $date = $nextAvailabilitySlot->getStartDate();

            if ($locale === 'fr') {
                $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'd MMMM');
                $formattedDate = $formatter->format($date);
            } else {
                $formatter = new IntlDateFormatter('en_US', IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'd MMMM');
                $formattedDate = $formatter->format($date);
            }
        }

        return $this->render('home/index.html.twig', [
            'featuredGites' => $featuredGites,
            'services' => $services,
            'nextAvailabilitySlot' => $nextAvailabilitySlot,
            'formattedDate' => $formattedDate,
        ]);
    }
}
