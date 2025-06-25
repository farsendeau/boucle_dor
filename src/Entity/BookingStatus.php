<?php

namespace App\Entity;

enum BookingStatus: string
{
    case PENDING = 'pending';
    case VALIDATED = 'validated';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match($this) {
            self::PENDING => 'En attente',
            self::VALIDATED => 'Validée',
            self::REJECTED => 'Rejetée',
            self::CANCELLED => 'Annulée',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::VALIDATED => 'success',
            self::REJECTED => 'danger',
            self::CANCELLED => 'secondary',
        };
    }
}