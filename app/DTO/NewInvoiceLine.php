<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * One requested invoice line, already shape validated at the HTTP
 * boundary. Business checks (positive amount, quantity) happen in the
 * action, not here.
 */
final readonly class NewInvoiceLine
{
    public function __construct(
        public string $description,
        public int $quantity,
        public int $unitAmountMinorUnits,
    ) {}
}
