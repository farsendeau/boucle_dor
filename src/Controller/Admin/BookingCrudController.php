<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use App\Entity\BookingStatus;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

class BookingCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Réservation')
            ->setEntityLabelInPlural('Réservations')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(25);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id', 'ID')
                ->hideOnForm(),

            TextField::new('firstname', 'Prénom')
                ->setRequired(true),

            TextField::new('lastname', 'Nom')
                ->setRequired(true),

            EmailField::new('mail', 'Email')
                ->setRequired(true),

            AssociationField::new('gite', 'Gîte')
                ->setRequired(true),

            DateField::new('dateArrival', 'Date d\'arrivée')
                ->setRequired(true),

            DateField::new('dateDeparture', 'Date de départ')
                ->setRequired(true),

            IntegerField::new('nbAdult', 'Nb adultes')
                ->setRequired(true),

            IntegerField::new('nbChild', 'Nb enfants'),

            MoneyField::new('price', 'Prix nuitée')
                ->setCurrency('EUR')
                ->setStoredAsCents(false)
                ->hideOnForm(),

            MoneyField::new('totalPrice', 'Prix total')
                ->setCurrency('EUR')
                ->setStoredAsCents(false)
                ->hideOnForm(),

            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'En attente' => BookingStatus::PENDING,
                    'Validée' => BookingStatus::VALIDATED,
                    'Rejetée' => BookingStatus::REJECTED,
                    'Annulée' => BookingStatus::CANCELLED,
                ])
                ->renderAsBadges([
                    BookingStatus::PENDING->value => 'warning',
                    BookingStatus::VALIDATED->value => 'success',
                    BookingStatus::REJECTED->value => 'danger',
                    BookingStatus::CANCELLED->value => 'secondary',
                ])
                ->hideOnForm(),

            DateTimeField::new('createdAt', 'Créée le')
                ->hideOnForm(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('status', 'Statut')->setChoices([
                'En attente' => BookingStatus::PENDING,
                'Validée' => BookingStatus::VALIDATED,
                'Rejetée' => BookingStatus::REJECTED,
                'Annulée' => BookingStatus::CANCELLED,
            ]))
            ->add(EntityFilter::new('gite', 'Gîte'))
            ->add(DateTimeFilter::new('dateArrival', 'Date d\'arrivée'))
            ->add(DateTimeFilter::new('dateDeparture', 'Date de départ'))
            ->add(DateTimeFilter::new('createdAt', 'Date de création'));
    }

    public function configureActions(Actions $actions): Actions
    {
        $validateAction = Action::new('validate', 'Valider', 'fa fa-check')
            ->linkToRoute('admin_booking_validate', function (Booking $booking) {
                return ['id' => $booking->getId()];
            })
            ->setCssClass('btn btn-success')
            ->displayIf(function (Booking $booking) {
                return $booking->getStatus() === BookingStatus::PENDING;
            });

        $rejectAction = Action::new('reject', 'Rejeter', 'fa fa-times')
            ->linkToRoute('admin_booking_reject', function (Booking $booking) {
                return ['id' => $booking->getId()];
            })
            ->setCssClass('btn btn-danger')
            ->displayIf(function (Booking $booking) {
                return $booking->getStatus() === BookingStatus::PENDING;
            });

        return $actions
            ->add(Crud::PAGE_INDEX, $validateAction)
            ->add(Crud::PAGE_INDEX, $rejectAction)
            ->add(Crud::PAGE_DETAIL, $validateAction)
            ->add(Crud::PAGE_DETAIL, $rejectAction)
            ->disable(Action::NEW)
            ->disable(Action::DELETE);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Booking) {
            if ($entityInstance->getGite() && !$entityInstance->getPrice()) {
                $entityInstance->setPrice($entityInstance->getGite()->getPrice());
            }
            $entityInstance->calculateTotalPrice();
        }
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Booking) {
            $entityInstance->calculateTotalPrice();
        }
        parent::updateEntity($entityManager, $entityInstance);
    }

    #[Route('/admin/booking/validate/{id}', name: 'admin_booking_validate', methods: ['GET'])]
    public function validateBooking(int $id): Response
    {
        $booking = $this->entityManager->getRepository(Booking::class)->find($id);

        if (!$booking) {
            $this->addFlash('error', 'Réservation introuvable.');
            return $this->redirect($this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
            );
        }

        if ($booking->getStatus() !== BookingStatus::PENDING) {
            $this->addFlash('error', 'Cette réservation ne peut plus être validée.');
            return $this->redirect($this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
            );
        }

        $booking->setStatus(BookingStatus::VALIDATED);
        $this->entityManager->flush();

        $this->addFlash('success', 'La réservation a été validée avec succès.');

        return $this->redirect($this->adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl()
        );
    }

    #[Route('/admin/booking/reject/{id}', name: 'admin_booking_reject', methods: ['GET'])]
    public function rejectBooking(int $id): Response
    {
        $booking = $this->entityManager->getRepository(Booking::class)->find($id);
        if (!$booking) {
            $this->addFlash('error', 'Réservation introuvable.');
            return $this->redirect($this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
            );
        }

        if ($booking->getStatus() !== BookingStatus::PENDING) {
            $this->addFlash('error', 'Cette réservation ne peut plus être rejetée.');
            return $this->redirect($this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
            );
        }

        $booking->setStatus(BookingStatus::REJECTED);
        $this->entityManager->flush();

        $this->addFlash('success', 'La réservation a été rejetée.');

        return $this->redirect($this->adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl()
        );
    }
}
