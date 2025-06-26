<?php

namespace App\Repository\Gite;

use App\DTO\AvailabilitySlot;
use App\Entity\Gite\Gite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Gite>
 */
class GiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gite::class);
    }

    public function findNextAvailableWeekSlot(): ?AvailabilitySlot
    {
        $gites = $this->findAll();
        $earliestSlot = null;
        
        foreach ($gites as $gite) {
            $slot = $this->findFirstAvailableWeekForGite($gite);
            if ($slot && (!$earliestSlot || $slot->getStartDate() < $earliestSlot->getStartDate())) {
                $earliestSlot = $slot;
            }
        }
        
        return $earliestSlot;
    }
    
    private function findFirstAvailableWeekForGite(Gite $gite): ?AvailabilitySlot
    {
        $startDate = new \DateTime();
        $maxSearchDays = 365; // Limite de recherche à 1 an
        
        // Récupérer toutes les réservations validées du gîte
        $bookings = $this->getEntityManager()
            ->getRepository(\App\Entity\Booking::class)
            ->createQueryBuilder('b')
            ->where('b.gite = :gite')
            ->andWhere('b.status = :status')
            ->andWhere('b.dateDeparture >= :today')
            ->setParameter('gite', $gite)
            ->setParameter('status', 'validated')
            ->setParameter('today', $startDate)
            ->orderBy('b.dateArrival', 'ASC')
            ->getQuery()
            ->getResult();
        
        for ($day = 0; $day < $maxSearchDays; $day++) {
            $checkDate = (clone $startDate)->modify("+$day days");
            $endDate = (clone $checkDate)->modify('+7 days');
            
            $hasConflict = false;
            foreach ($bookings as $booking) {
                // Vérifier si le créneau de 7 jours entre en conflit avec une réservation
                if ($booking->getDateArrival() < $endDate && $booking->getDateDeparture() > $checkDate) {
                    $hasConflict = true;
                    break;
                }
            }
            
            if (!$hasConflict) {
                return new AvailabilitySlot($gite, $checkDate, $endDate);
            }
        }
        
        return null;
    }
}
