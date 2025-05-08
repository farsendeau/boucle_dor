<?php

namespace App\Controller;

use App\Repository\Site\LegalPageRepository;
use http\Exception\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/legal-page')]
final class LegalPageController extends AbstractController
{
    public function __construct(
        private readonly LegalPageRepository $legalPageRepository,
    ) {
    }

    #[Route('/{page}', name: 'legal_page_index', methods: ['GET'])]
    public function index(Request $request, string $page): Response
    {
        $legalPage = $this->legalPageRepository->findOneBy([]);
        if (!$legalPage) {
            $referer = $request->headers->get('referer');

            return $this->redirect($referer);
        }

        $methodTitle = sprintf('get%sTitle%s', ucfirst($page), ucfirst($request->getLocale()));
        if (!method_exists($legalPage, $methodTitle)) {
            throw new InvalidArgumentException($methodTitle);
        }

        $methodContent = sprintf('get%sContent%s', ucfirst($page), ucfirst($request->getLocale()));
        if (!method_exists($legalPage, $methodContent)) {
            throw new InvalidArgumentException($methodContent);
        }

        return $this->render('legal_page/index.html.twig', [
            'title' => $legalPage->$methodTitle(),
            'content' => $legalPage->$methodContent(),
            'page' => $page,
        ]);
    }
}
