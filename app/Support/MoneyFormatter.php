<?php

declare(strict_types=1);

namespace App\Support;

use App\Domain\Money;

/**
 * Formats a Money value for humans. Known currencies get their symbol
 * ("$12.99"), everything else falls back to the code ("AUD 12.99").
 * Thousands are grouped, minor units always shown to two places.
 */
final class MoneyFormatter
{
    /** @var array<string, string> */
    private const SYMBOLS = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
    ];

    public function format(Money $money): string
    {
        $amount = number_format($money->toDecimal(), 2, '.', ',');

        $symbol = self::SYMBOLS[$money->currency] ?? null;

        return $symbol !== null
            ? $symbol.$amount
            : $money->currency.' '.$amount;
    }
}
