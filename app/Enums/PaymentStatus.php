<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Waiting = 'waiting';
    case Paid = 'paid';
    case Canceled = 'canceled';

    public function label(): string
    {
        return match ($this) {
            self::Waiting => 'Waiting for Payment',
            self::Paid => 'Success',
            self::Canceled => 'Canceled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Waiting => 'status-badge status-waiting',
            self::Paid => 'status-badge status-paid',
            self::Canceled => 'status-badge status-canceled',
        };
    }
}
