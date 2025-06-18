<?php

namespace App\Validator\Constraints;

use App\DTO\Booking;
use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidBookingDatesValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidBookingDates) {
            throw new UnexpectedTypeException($constraint, ValidBookingDates::class);
        }

        if (!$value instanceof Booking) {
            return;
        }

        $dateArrival = $value->getDateArrival();
        $dateDeparture = $value->getDateDeparture();

        // Si les deux dates ne sont pas définies, on ne peut pas valider
        if (!$dateArrival || !$dateDeparture) {
            return;
        }

        $today = new DateTime('today');

        // Vérification que la date d'arrivée n'est pas antérieure à aujourd'hui
        if ($dateArrival < $today) {
            $this->context->buildViolation($constraint->arrivalTooEarly)
                ->atPath('dateArrival')
                ->addViolation();
        }

        // Vérification que les dates ne sont pas identiques
        if ($dateArrival->format('Y-m-d') === $dateDeparture->format('Y-m-d')) {
            $this->context->buildViolation($constraint->sameDate)
                ->atPath('dateDeparture')
                ->addViolation();
        }

        // Vérification que la date d'arrivée est antérieure à la date de départ
        if ($dateArrival >= $dateDeparture) {
            $this->context->buildViolation($constraint->message)
                ->atPath('dateDeparture')
                ->addViolation();
        }
    }
}