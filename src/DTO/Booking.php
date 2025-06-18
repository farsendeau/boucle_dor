<?php

namespace App\DTO;

use App\Validator\Constraints\ValidBookingDates;
use DateTime;

#[ValidBookingDates]
class Booking
{
    public function __construct(
        public ?string $lastname = null,
        public ?string $firstname = null,
        public ?string $mail = null,
        public ?int $nbAdult = null,
        public ?int $nbChild = null,
        public ?DateTime $dateArrival = null,
        public ?DateTime $dateDeparture = null,
        public string $giteName = '',
        public int $price = 0
    ) {
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): void
    {
        $this->mail = $mail;
    }

    public function getNbAdult(): ?int
    {
        return $this->nbAdult;
    }

    public function setNbAdult(?int $nbAdult): void
    {
        $this->nbAdult = $nbAdult;
    }

    public function getNbChild(): ?int
    {
        return $this->nbChild;
    }

    public function setNbChild(?int $nbChild): void
    {
        $this->nbChild = $nbChild;
    }

    public function getDateArrival(): ?DateTime
    {
        return $this->dateArrival;
    }

    public function setDateArrival(?DateTime $dateArrival): void
    {
        $this->dateArrival = $dateArrival;
    }

    public function getDateDeparture(): ?DateTime
    {
        return $this->dateDeparture;
    }

    public function setDateDeparture(?DateTime $dateDeparture): void
    {
        $this->dateDeparture = $dateDeparture;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    public function getGiteName(): string
    {
        return $this->giteName;
    }

    public function setGiteName(string $giteName): void
    {
        $this->giteName = $giteName;
    }
}
