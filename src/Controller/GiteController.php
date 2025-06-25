<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Gite\Gite;
use App\Form\BookingFormType;
use App\Repository\BookingRepository;
use App\Repository\Gite\GiteRepository;
use App\Repository\Site\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gite')]
final class GiteController extends AbstractController
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
        private readonly MailerInterface $mailer,
        private readonly GiteRepository $giteRepository,
        private readonly BookingRepository $bookingRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/index', name: 'gite_index')]
    public function index(): Response
    {
        $gites = $this->giteRepository->findAll();

        return $this->render('gite/index.html.twig', [
            'gites' => $gites,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/{slug:gite}', name: 'gite_show')]
    public function show(Request $request, Gite $gite): Response
    {
        $booking = new Booking();
        $booking->setGite($gite);
        $form = $this->createForm(BookingFormType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if dates are available
            if (!$this->bookingRepository->areDatesAvailable(
                $gite,
                $booking->getDateArrival(),
                $booking->getDateDeparture()
            )) {
                $this->addFlash('error', 'Les dates sélectionnées ne sont pas disponibles.');
                return $this->redirectToRoute('gite_show', ['slug' => $gite->getSlug()]);
            }

            // Calculate total price
            $booking->calculateTotalPrice();

            // Persist booking
            $this->bookingRepository->save($booking, true);

            // Send email with booking ID
            $site = $this->siteRepository->findOneBy([]);
            $fromMail = $site ? $site->getMail() : '';
            $fromName = $site ? $site->getName() : '';

            $subject = sprintf('%s - Nouvelle réservation #%d - %s',
                $site->getName() ?? '',
                $booking->getId(),
                $gite->getName()
            );

            $email = (new TemplatedEmail())
                ->from(new Address($fromMail, $fromName))
                ->to($site->getMail())
                ->subject($subject)
                ->htmlTemplate('gite/email.html.twig')
                ->context([
                    'booking' => $booking,
                    'gite' => $gite
                ]);

            $this->mailer->send($email);

            $this->addFlash('success', 'Votre demande de réservation a été envoyée avec succès. Vous recevrez une confirmation par email.');

            return $this->redirectToRoute('gite_show', ['slug' => $gite->getSlug()]);
        }

        return $this->render('gite/show.html.twig', [
            'gite' => $gite,
            'form' => $form,
        ]);
    }
}
