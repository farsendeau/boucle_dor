<?php

namespace App\Entity;

use App\Entity\Gite\Gite;
use App\Repository\BookingRepository;
use App\Validator\Constraints\ValidBookingDates;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: 'booking')]
#[ORM\Index(columns: ['date_arrival', 'date_departure'], name: 'booking_dates_idx')]
#[ORM\Index(columns: ['status'], name: 'booking_status_idx')]
#[ValidBookingDates]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $mail = null;

    #[ORM\Column]
    private ?int $nbAdult = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbChild = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?DateTime $dateArrival = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?DateTime $dateDeparture = null;

    #[ORM\Column]
    private ?int $totalPrice = null;

    #[ORM\Column(length: 50, enumType: BookingStatus::class)]
    private ?BookingStatus $status = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Gite $gite = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTime $createdAt = null;

    public function __construct()
    {
        $this->status = BookingStatus::PENDING;
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;
        return $this;
    }

    public function getNbAdult(): ?int
    {
        return $this->nbAdult;
    }

    public function setNbAdult(int $nbAdult): static
    {
        $this->nbAdult = $nbAdult;
        return $this;
    }

    public function getNbChild(): ?int
    {
        return $this->nbChild;
    }

    public function setNbChild(?int $nbChild): static
    {
        $this->nbChild = $nbChild;
        return $this;
    }

    public function getDateArrival(): ?DateTime
    {
        return $this->dateArrival;
    }

    public function setDateArrival(DateTime $dateArrival): static
    {
        $this->dateArrival = $dateArrival;
        return $this;
    }

    public function getDateDeparture(): ?DateTime
    {
        return $this->dateDeparture;
    }

    public function setDateDeparture(DateTime $dateDeparture): static
    {
        $this->dateDeparture = $dateDeparture;
        return $this;
    }

    public function getTotalPrice(): ?int
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(int $totalPrice): static
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function getStatus(): ?BookingStatus
    {
        return $this->status;
    }

    public function setStatus(BookingStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getGite(): ?Gite
    {
        return $this->gite;
    }

    public function setGite(?Gite $gite): static
    {
        $this->gite = $gite;
        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function calculateTotalPrice(): void
    {
        if ($this->dateArrival && $this->dateDeparture && $this->gite) {
            $days = $this->dateArrival->diff($this->dateDeparture)->days;
            $this->totalPrice = $days * $this->gite->getPrice();
        }
    }

    public function __toString(): string
    {
        return sprintf(
            '%s %s - %s (%s)',
            $this->firstname,
            $this->lastname,
            $this->gite?->getName() ?? 'N/A',
            $this->status?->value ?? 'N/A'
        );
    }
}