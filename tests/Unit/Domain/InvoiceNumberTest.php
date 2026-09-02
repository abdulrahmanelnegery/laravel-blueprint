<?php

declare(strict_types=1);

use App\Domain\InvoiceNumber;

it('formats parts into a zero padded string', function (): void {
    expect(InvoiceNumber::fromParts('inv', 2026, 7)->toString())->toBe('INV-2026-0007');
});

it('round trips through a string', function (): void {
    $number = InvoiceNumber::fromString('INV-2026-0042');

    expect($number->prefix)->toBe('INV')
        ->and($number->year)->toBe(2026)
        ->and($number->sequence)->toBe(42);
});

it('rejects a malformed value', function (): void {
    InvoiceNumber::fromString('not-a-number');
})->throws(InvalidArgumentException::class);

it('rejects a non positive sequence', function (): void {
    InvoiceNumber::fromParts('INV', 2026, 0);
})->throws(InvalidArgumentException::class);
