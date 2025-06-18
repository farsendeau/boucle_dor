<?php

declare(strict_types=1);

namespace App\Tests\Unit\DTO;

use App\DTO\Booking;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookingValidationTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidBooking(): void
    {
        $booking = new Booking();
        $booking->setLastname('Dupont');
        $booking->setFirstname('Jean');
        $booking->setMail('jean.dupont@example.com');
        $booking->setNbAdult(2);
        $booking->setNbChild(1);
        $booking->setDateArrival(new DateTime('+1 day'));
        $booking->setDateDeparture(new DateTime('+3 days'));

        $violations = $this->validator->validate($booking);

        $this->assertCount(0, $violations);
    }

    public function testInvalidBookingArrivalAfterDeparture(): void
    {
        $booking = new Booking();
        $booking->setLastname('Dupont');
        $booking->setFirstname('Jean');
        $booking->setMail('jean.dupont@example.com');
        $booking->setNbAdult(2);
        $booking->setNbChild(1);
        $booking->setDateArrival(new DateTime('+5 days'));
        $booking->setDateDeparture(new DateTime('+3 days'));

        $violations = $this->validator->validate($booking);

        $this->assertGreaterThan(0, $violations->count());
        
        $foundExpectedViolation = false;
        foreach ($violations as $violation) {
            if ($violation->getMessageTemplate() === 'booking.dates.arrival_before_departure') {
                $foundExpectedViolation = true;
                break;
            }
        }
        
        $this->assertTrue($foundExpectedViolation, 'Expected violation for arrival after departure not found');
    }

    public function testInvalidBookingArrivalInPast(): void
    {
        $booking = new Booking();
        $booking->setLastname('Dupont');
        $booking->setFirstname('Jean');
        $booking->setMail('jean.dupont@example.com');
        $booking->setNbAdult(2);
        $booking->setNbChild(1);
        $booking->setDateArrival(new DateTime('-1 day'));
        $booking->setDateDeparture(new DateTime('+3 days'));

        $violations = $this->validator->validate($booking);

        $this->assertGreaterThan(0, $violations->count());
        
        $foundExpectedViolation = false;
        foreach ($violations as $violation) {
            if ($violation->getMessageTemplate() === 'booking.dates.arrival_too_early') {
                $foundExpectedViolation = true;
                break;
            }
        }
        
        $this->assertTrue($foundExpectedViolation, 'Expected violation for arrival in past not found');
    }

    public function testInvalidBookingSameDates(): void
    {
        $booking = new Booking();
        $booking->setLastname('Dupont');
        $booking->setFirstname('Jean');
        $booking->setMail('jean.dupont@example.com');
        $booking->setNbAdult(2);
        $booking->setNbChild(1);
        
        $sameDate = new DateTime('+1 day');
        $booking->setDateArrival($sameDate);
        $booking->setDateDeparture(clone $sameDate);

        $violations = $this->validator->validate($booking);

        $this->assertGreaterThan(0, $violations->count());
        
        $foundExpectedViolation = false;
        foreach ($violations as $violation) {
            if ($violation->getMessageTemplate() === 'booking.dates.same_date') {
                $foundExpectedViolation = true;
                break;
            }
        }
        
        $this->assertTrue($foundExpectedViolation, 'Expected violation for same dates not found');
    }

}