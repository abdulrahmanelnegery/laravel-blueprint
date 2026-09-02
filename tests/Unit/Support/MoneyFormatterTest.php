<?php

declare(strict_types=1);

use App\Domain\Money;
use App\Support\MoneyFormatter;

function formatMoney(Money $money): string
{
    return (new MoneyFormatter)->format($money);
}

it('formats a known currency with its symbol', function (): void {
    expect(formatMoney(Money::of(1299, 'USD')))->toBe('$12.99');
});

it('groups thousands', function (): void {
    expect(formatMoney(Money::of(123456, 'USD')))->toBe('$1,234.56');
});

it('always shows two minor digits', function (): void {
    expect(formatMoney(Money::of(500, 'EUR')))->toBe('€5.00');
});

it('falls back to the code for an unknown currency', function (): void {
    expect(formatMoney(Money::of(1000, 'AUD')))->toBe('AUD 10.00');
});
