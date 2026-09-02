<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * A formatted invoice number such as "INV-2026-0007": a letter prefix,
 * the issuing year, and a zero padded sequence that is unique within
 * that year. Immutable and validated on construction, so any
 * InvoiceNumber in the system is well formed by definition.
 */
final readonly class InvoiceNumber
{
    private const PATTERN = '/^(?<prefix>[A-Z]{2,6})-(?<year>\d{4})-(?<sequence>\d{4,})$/';

    private function __construct(
        public string $prefix,
        public int $year,
        public int $sequence,
    ) {}

    public static function fromParts(string $prefix, int $year, int $sequence): self
    {
        $prefix = strtoupper($prefix);

        if (preg_match('/^[A-Z]{2,6}$/', $prefix) !== 1) {
            throw new InvalidArgumentException("Invoice number prefix must be 2 to 6 letters, got \"{$prefix}\".");
        }

        if ($year < 2000 || $year > 9999) {
            throw new InvalidArgumentException("Invoice number year is out of range, got {$year}.");
        }

        if ($sequence < 1) {
            throw new InvalidArgumentException("Invoice number sequence must be positive, got {$sequence}.");
        }

        return new self($prefix, $year, $sequence);
    }

    public static function fromString(string $value): self
    {
        if (preg_match(self::PATTERN, $value, $matches) !== 1) {
            throw new InvalidArgumentException("Malformed invoice number \"{$value}\".");
        }

        return self::fromParts($matches['prefix'], (int) $matches['year'], (int) $matches['sequence']);
    }

    public function toString(): string
    {
        return sprintf('%s-%04d-%04d', $this->prefix, $this->year, $this->sequence);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
