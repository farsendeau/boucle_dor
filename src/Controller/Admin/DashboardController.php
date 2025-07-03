<?php

namespace App\Controller\Admin;

use App\Entity\Activity;
use App\Entity\Booking;
use App\Entity\Gite\Gite;
use App\Entity\Services;
use App\Entity\Site\LegalPage;
use App\Entity\Site\Site;
use App\Entity\User;
use App\Repository\BookingRepository;
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
        private readonly BookingRepository $bookingRepository,
    ) {
    }

    public function index(): Response
    {
        $currentBookings = $this->bookingRepository->findCurrentBookings();
        $upcomingBookings = $this->bookingRepository->findUpcomingBookings();
        $revenueData = $this->prepareRevenueData();

        return $this->render('admin/dashboard.html.twig', [
            'currentBookings' => $currentBookings,
            'upcomingBookings' => $upcomingBookings,
            'revenueData' => json_encode($revenueData),
        ]);

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
        yield MenuItem::section('Admin', 'fa fa-tools');
        yield MenuItem::linkToDashboard('Dashboard');
        yield MenuItem::linkToRoute('Retour au site', null, 'home_index');
        yield MenuItem::linkToCrud('Utilisateur Admin', null, User::class);

        yield MenuItem::section('Site', 'fa fa-cog');
        yield MenuItem::linkToCrud('Configuration du site', null, Site::class);
        yield MenuItem::linkToCrud('Pages légales', null, LegalPage::class);

        yield MenuItem::section('Gîte', 'fa fa-bed');
        yield MenuItem::linkToCrud('Gestion des Gîtes', null, Gite::class);
        yield MenuItem::linkToCrud('Réservations', null, Booking::class);

        yield MenuItem::section('Services', 'fa fa-wrench');
        yield MenuItem::linkToCrud('Gestion des Services', null, Services::class);
        yield MenuItem::linkToCrud('Gestion des Activités', null, Activity::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css')
            ->addHtmlContentToHead('<style>.trix-button--icon-code { display: none !important; }</style>');
    }

    private function prepareRevenueData(): array
    {
        $revenueData = $this->bookingRepository->getRevenueByGiteAndMonth();

        // Prepare data structure
        $gites = [];
        $months = [];
        $dataByGite = [];

        // Extract all gites and months
        foreach ($revenueData as $data) {
            $giteName = $data['gite_name'];
            $monthKey = $data['year'] . '-' . str_pad($data['month'], 2, '0', STR_PAD_LEFT);

            if (!in_array($giteName, $gites)) {
                $gites[] = $giteName;
            }

            if (!in_array($monthKey, $months)) {
                $months[] = $monthKey;
            }

            $dataByGite[$giteName][$monthKey] = (float) $data['total_revenue'];
        }

        sort($months);

        // Convert month keys to readable labels
        $monthLabels = array_map(function($monthKey) {
            [$year, $month] = explode('-', $monthKey);
            $monthNames = [
                '01' => 'Jan', '02' => 'Fév', '03' => 'Mar', '04' => 'Avr',
                '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Aoû',
                '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Déc'
            ];
            return $monthNames[$month] . ' ' . $year;
        }, $months);

        // Prepare datasets for each gite
        $datasets = [];
        $colors = [
            'rgba(255, 99, 132, 0.8)',
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 205, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(255, 159, 64, 0.8)'
        ];

        foreach ($gites as $index => $gite) {
            $giteData = [];
            foreach ($months as $month) {
                $giteData[] = $dataByGite[$gite][$month] ?? 0;
            }

            $datasets[] = [
                'label' => $gite,
                'data' => $giteData,
                'backgroundColor' => $colors[$index % count($colors)],
                'borderColor' => str_replace('0.8', '1', $colors[$index % count($colors)]),
                'borderWidth' => 1
            ];
        }

        return [
            'labels' => $monthLabels,
            'datasets' => $datasets
        ];
    }

}
