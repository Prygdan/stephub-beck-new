<?php

namespace App;

enum PaymentMethod: string
{
    case Card = 'card';
    case Overpayment = 'overpayment';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
