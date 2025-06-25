<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\BookingStatus;
use App\Entity\Gite\Gite;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function save(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Check if dates are available for a specific gite
     *
     * @param Gite $gite
     * @param DateTime $dateArrival
     * @param DateTime $dateDeparture
     * @param Booking|null $excludeBooking Exclude a specific booking from the check (for updates)
     * @return bool
     */
    public function areDatesAvailable(Gite $gite, DateTime $dateArrival, DateTime $dateDeparture, ?Booking $excludeBooking = null): bool
    {
        $qb = $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.gite = :gite')
            ->andWhere('b.status IN (:validStatuses)')
            ->andWhere('(
                (b.dateArrival <= :dateArrival AND b.dateDeparture > :dateArrival) OR
                (b.dateArrival < :dateDeparture AND b.dateDeparture >= :dateDeparture) OR
                (b.dateArrival >= :dateArrival AND b.dateDeparture <= :dateDeparture)
            )')
            ->setParameter('gite', $gite)
            ->setParameter('dateArrival', $dateArrival)
            ->setParameter('dateDeparture', $dateDeparture)
            ->setParameter('validStatuses', [BookingStatus::PENDING, BookingStatus::VALIDATED]);

        if ($excludeBooking) {
            $qb->andWhere('b.id != :excludeId')
               ->setParameter('excludeId', $excludeBooking->getId());
        }

        return (int) $qb->getQuery()->getSingleScalarResult() === 0;
    }

    /**
     * Find bookings by status
     *
     * @param BookingStatus $status
     * @return Booking[]
     */
    public function findByStatus(BookingStatus $status): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.status = :status')
            ->setParameter('status', $status)
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all bookings for a specific gite
     *
     * @param Gite $gite
     * @return Booking[]
     */
    public function findByGite(Gite $gite): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.gite = :gite')
            ->setParameter('gite', $gite)
            ->orderBy('b.dateArrival', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find validated bookings for a specific gite and date range
     * Used to block dates in the frontend
     *
     * @param Gite $gite
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function findValidatedBookingDates(Gite $gite, DateTime $startDate, DateTime $endDate): array
    {
        $bookings = $this->createQueryBuilder('b')
            ->select('b.dateArrival, b.dateDeparture')
            ->andWhere('b.gite = :gite')
            ->andWhere('b.status = :status')
            ->andWhere('b.dateDeparture >= :startDate')
            ->andWhere('b.dateArrival <= :endDate')
            ->setParameter('gite', $gite)
            ->setParameter('status', BookingStatus::VALIDATED)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();

        $blockedDates = [];
        foreach ($bookings as $booking) {
            $current = clone $booking['dateArrival'];
            while ($current < $booking['dateDeparture']) {
                $blockedDates[] = $current->format('Y-m-d');
                $current->modify('+1 day');
            }
        }

        return array_unique($blockedDates);
    }

    /**
     * Find pending bookings count
     *
     * @return int
     */
    public function countPendingBookings(): int
    {
        return (int) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere('b.status = :status')
            ->setParameter('status', BookingStatus::PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Find current bookings (validated and currently ongoing)
     *
     * @return Booking[]
     */
    public function findCurrentBookings(): array
    {
        $today = new DateTime();

        return $this->createQueryBuilder('b')
            ->select('partial b.{id, firstname, lastname, mail, dateArrival, dateDeparture, totalPrice}')
            ->join('b.gite', 'g')
            ->addSelect('partial g.{id, name, backgroundImageName}')
            ->andWhere('b.status = :status')
            ->andWhere('b.dateArrival <= :today')
            ->andWhere('b.dateDeparture > :today')
            ->setParameter('status', BookingStatus::VALIDATED)
            ->setParameter('today', $today)
            ->orderBy('b.dateArrival', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find upcoming bookings (validated and starting in the future)
     *
     * @return Booking[]
     */
    public function findUpcomingBookings(): array
    {
        $today = new DateTime();

        return $this->createQueryBuilder('b')
            ->select('partial b.{id, firstname, lastname, mail, dateArrival, dateDeparture, totalPrice}')
            ->join('b.gite', 'g')
            ->addSelect('partial g.{id, name, backgroundImageName}')
            ->andWhere('b.status = :status')
            ->andWhere('b.dateArrival > :today')
            ->setParameter('status', BookingStatus::VALIDATED)
            ->setParameter('today', $today)
            ->orderBy('b.dateArrival', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get revenue statistics by gite and month for the last 12 months
     *
     * @return array
     */
    public function getRevenueByGiteAndMonth(): array
    {
        // Période : 12 mois précédents (excluant le mois en cours)
        $startDate = new DateTime('-12 months');
        $startDate->modify('first day of this month');

        // Fin : dernier jour du mois précédent
        $endDate = new DateTime('first day of this month');
        $endDate->modify('-1 day');

        $bookings = $this->createQueryBuilder('b')
            ->select('g.name as gite_name, b.dateArrival, b.totalPrice')
            ->join('b.gite', 'g')
            ->where('b.status = :status')
            ->andWhere('b.dateArrival >= :startDate')
            ->andWhere('b.dateArrival <= :endDate')
            ->setParameter('status', BookingStatus::VALIDATED)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('b.dateArrival', 'ASC')
            ->getQuery()
            ->getResult();


        // Group by gite and month in PHP
        $groupedData = [];
        foreach ($bookings as $booking) {
            $giteName = $booking['gite_name'];
            $date = $booking['dateArrival'];
            $year = (int) $date->format('Y');
            $month = (int) $date->format('m');
            $price = (float) $booking['totalPrice'];

            $key = $giteName . '_' . $year . '_' . $month;

            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [
                    'gite_name' => $giteName,
                    'year' => $year,
                    'month' => $month,
                    'total_revenue' => 0
                ];
            }

            $groupedData[$key]['total_revenue'] += $price;
        }

        return array_values($groupedData);
    }
}
