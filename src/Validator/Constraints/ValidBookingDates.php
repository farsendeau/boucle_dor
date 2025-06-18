<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidBookingDates extends Constraint
{
    public string $message = 'booking.dates.arrival_before_departure';
    public string $sameDate = 'booking.dates.same_date';
    public string $arrivalTooEarly = 'booking.dates.arrival_too_early';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}