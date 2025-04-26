<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LocaleController extends AbstractController
{
    #[Route('/locale/{locale}', name: 'locale_change', requirements: ['locale' => 'fr|en'])]
    public function change(Request $request, $locale): Response
    {
        $request->getSession()->set('_locale', $locale);
        $referer = $request->headers->get('referer');

        return $this->redirect($referer ?: $this->generateUrl('home_index'));
    }
}
