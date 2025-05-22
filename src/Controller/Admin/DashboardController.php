<?php

namespace App\Controller\Admin;

use App\Entity\Site\LegalPage;
use App\Entity\Site\Site;
use App\Entity\User;
use App\Repository\Site\SiteRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * https://scqr.net/en/blog/2022/11/11/symfony-6-and-easyadmin-4-admin-panel-for-user-management-system/
 */
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
    ) {
    }

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        $site = $this->siteRepository->findOneBy([]);
        $name = $site ? $site->getName() : 'App';

        return Dashboard::new()
            ->setTitle($name);

    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-home', 'home_index');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-tachometer');
        yield MenuItem::linkToCrud('Utilisateur Admin', 'fa fa-list', User::class);

        yield MenuItem::section('Site', 'fa fa-cog');
        yield MenuItem::linkToCrud('Configuration du site', null, Site::class);
        yield MenuItem::linkToCrud('Pages légales', null, LegalPage::class);

        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addHtmlContentToHead('<style>.trix-button--icon-code { display: none !important; }</style>');
    }
}
