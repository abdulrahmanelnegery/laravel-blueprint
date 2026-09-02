<?php

declare(strict_types=1);

use App\Domain\Money;
use App\Exceptions\Domain\CurrencyMismatchException;

it('adds two amounts of the same currency into a new instance', function (): void {
    $a = Money::of(1000, 'USD');
    $b = Money::of(250, 'USD');

    $sum = $a->add($b);

    expect($sum->minorUnits)->toBe(1250)
        ->and($a->minorUnits)->toBe(1000) // original is untouched
        ->and($sum)->not->toBe($a);
});

it('multiplies by a whole factor', function (): void {
    expect(Money::of(199, 'EUR')->multiply(3)->minorUnits)->toBe(597);
});

it('treats a negative amount as a programming error', function (): void {
    Money::of(-1, 'USD');
})->throws(InvalidArgumentException::class);

it('rejects a malformed currency code', function (): void {
    Money::of(100, 'us');
})->throws(InvalidArgumentException::class);

it('throws a domain exception when currencies differ on add', function (): void {
    Money::of(100, 'USD')->add(Money::of(100, 'EUR'));
})->throws(CurrencyMismatchException::class);

it('compares by value with equals', function (): void {
    expect(Money::of(500, 'USD')->equals(Money::of(500, 'USD')))->toBeTrue()
        ->and(Money::of(500, 'USD')->equals(Money::of(500, 'EUR')))->toBeFalse()
        ->and(Money::of(500, 'USD')->equals(Money::of(499, 'USD')))->toBeFalse();
});

it('normalises the currency code to upper case', function (): void {
    expect(Money::of(100, 'usd')->currency)->toBe('USD');
});

it('exposes a decimal representation for display', function (): void {
    expect(Money::of(1299, 'USD')->toDecimal())->toBe(12.99);
});
