<?php

namespace App\Controller;

use App\DTO\Contact;
use App\Entity\Site\Site;
use App\Form\ContactType;
use App\Repository\Site\SiteRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contact')]
final class ContactController extends AbstractController
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
    ) {
    }

    #[Route('/', name: 'contact_index', methods: ['GET', 'POST'])]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $site = $this->siteRepository->findOneBy([]);
            $fromMail = $site ? $site->getMail() : '';
            $fromName = $site ? $site->getName() : '';

            $subject = sprintf('%s - Contact', $site->getName() ?? '');

            $email = (new TemplatedEmail())
                ->from(new Address($fromMail, $fromName))
                ->to($site->getMail())
                ->subject($subject)
                ->htmlTemplate('contact/email.html.twig')
                ->context(['contact' => $contact]);

            $mailer->send($email);

            $this->addFlash('success', 'contact.mail_send_success');
            return $this->redirectToRoute('contact_index');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form,
        ]);
    }
}
