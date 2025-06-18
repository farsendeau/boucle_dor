<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\DTO\Booking;
use App\Validator\Constraints\ValidBookingDates;
use App\Validator\Constraints\ValidBookingDatesValidator;
use DateTime;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ValidBookingDatesValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ValidBookingDatesValidator
    {
        return new ValidBookingDatesValidator();
    }

    public function testValidBookingDates(): void
    {
        $booking = new Booking();
        $booking->setDateArrival(new DateTime('+1 day'));
        $booking->setDateDeparture(new DateTime('+3 days'));

        $this->validator->validate($booking, new ValidBookingDates());

        $this->assertNoViolation();
    }

    public function testInvalidBookingDatesArrivalAfterDeparture(): void
    {
        $booking = new Booking();
        $booking->setDateArrival(new DateTime('+5 days'));
        $booking->setDateDeparture(new DateTime('+3 days'));

        $constraint = new ValidBookingDates();
        $this->validator->validate($booking, $constraint);

        $this->buildViolation($constraint->message)
            ->atPath('property.path.dateDeparture')
            ->assertRaised();
    }

    public function testInvalidBookingDatesArrivalInPast(): void
    {
        $booking = new Booking();
        $booking->setDateArrival(new DateTime('-1 day'));
        $booking->setDateDeparture(new DateTime('+3 days'));

        $constraint = new ValidBookingDates();
        $this->validator->validate($booking, $constraint);

        $this->buildViolation($constraint->arrivalTooEarly)
            ->atPath('property.path.dateArrival')
            ->assertRaised();
    }

    public function testInvalidBookingDatesSameDate(): void
    {
        $booking = new Booking();
        $sameDate = new DateTime('+1 day');
        $booking->setDateArrival($sameDate);
        $booking->setDateDeparture(clone $sameDate);

        $constraint = new ValidBookingDates();
        $this->validator->validate($booking, $constraint);

        $violations = $this->context->getViolations();
        $this->assertCount(2, $violations);
        
        // Vérifier que les deux violations attendues sont présentes
        $violationMessages = [];
        foreach ($violations as $violation) {
            $violationMessages[] = $violation->getMessageTemplate();
        }
        
        $this->assertContains($constraint->sameDate, $violationMessages);
        $this->assertContains($constraint->message, $violationMessages);
    }

    public function testValidBookingDatesWithNullValues(): void
    {
        $booking = new Booking();
        $booking->setDateArrival(null);
        $booking->setDateDeparture(null);

        $this->validator->validate($booking, new ValidBookingDates());

        $this->assertNoViolation();
    }

    public function testValidBookingDatesWithOnlyArrivalDate(): void
    {
        $booking = new Booking();
        $booking->setDateArrival(new DateTime('+1 day'));
        $booking->setDateDeparture(null);

        $this->validator->validate($booking, new ValidBookingDates());

        $this->assertNoViolation();
    }

    public function testValidBookingDatesWithOnlyDepartureDate(): void
    {
        $booking = new Booking();
        $booking->setDateArrival(null);
        $booking->setDateDeparture(new DateTime('+3 days'));

        $this->validator->validate($booking, new ValidBookingDates());

        $this->assertNoViolation();
    }

    public function testValidBookingDatesMinimumStay(): void
    {
        $booking = new Booking();
        $today = new DateTime('today');
        $tomorrow = new DateTime('tomorrow');
        
        $booking->setDateArrival($today);
        $booking->setDateDeparture($tomorrow);

        $this->validator->validate($booking, new ValidBookingDates());

        $this->assertNoViolation();
    }
}