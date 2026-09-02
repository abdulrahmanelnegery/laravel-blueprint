<?php

declare(strict_types=1);

namespace App\Domain;

use App\Exceptions\Domain\CurrencyMismatchException;
use InvalidArgumentException;

/**
 * Money as an immutable value object: an integer amount in minor units
 * (cents, pence) plus an ISO 4217 currency code. Never negative.
 *
 * Arithmetic returns new instances. Two Money values only combine when
 * their currencies match. A currency mismatch is a recoverable business
 * error (CurrencyMismatchException). A negative amount or a malformed
 * currency code is a programming error (InvalidArgumentException) and is
 * not meant to be caught.
 */
final readonly class Money
{
    private function __construct(
        public int $minorUnits,
        public string $currency,
    ) {}

    public static function of(int $minorUnits, string $currency): self
    {
        if ($minorUnits < 0) {
            throw new InvalidArgumentException("Money cannot be negative, got {$minorUnits}.");
        }

        $code = strtoupper($currency);

        if (preg_match('/^[A-Z]{3}$/', $code) !== 1) {
            throw new InvalidArgumentException("Currency must be a 3 letter code, got \"{$currency}\".");
        }

        return new self($minorUnits, $code);
    }

    public static function zero(string $currency): self
    {
        return self::of(0, $currency);
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->minorUnits + $other->minorUnits, $this->currency);
    }

    public function multiply(int $factor): self
    {
        if ($factor < 0) {
            throw new InvalidArgumentException("Cannot multiply Money by a negative factor, got {$factor}.");
        }

        return new self($this->minorUnits * $factor, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->minorUnits === $other->minorUnits
            && $this->currency === $other->currency;
    }

    public function isZero(): bool
    {
        return $this->minorUnits === 0;
    }

    /**
     * Amount in major units, e.g. 1299 becomes 12.99. For display only;
     * keep arithmetic on the integer minor units.
     */
    public function toDecimal(): float
    {
        return (float) $this->minorUnits / 100;
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new CurrencyMismatchException($this->currency, $other->currency);
        }
    }
}
