<?php

declare(strict_types=1);

namespace App\DTO;

use App\Domain\Money;

/**
 * A single line prepared for rendering: quantities and Money value
 * objects, no Eloquent model behind it.
 */
final readonly class InvoiceLineSummary
{
    public function __construct(
        public string $description,
        public int $quantity,
        public Money $unitPrice,
        public Money $lineTotal,
    ) {}
}
