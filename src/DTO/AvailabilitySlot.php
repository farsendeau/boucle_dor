<?php

namespace App\DTO;

use App\Entity\Gite\Gite;
use DateTime;

class AvailabilitySlot
{
    public function __construct(
        private Gite $gite,
        private DateTime $startDate,
        private DateTime $endDate
    ) {
    }

    public function getGite(): Gite
    {
        return $this->gite;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }
}