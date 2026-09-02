<?php

declare(strict_types=1);

namespace App\Domain\Events;

use App\Domain\Money;

/**
 * Raised once an invoice is persisted. Carries only value data, so
 * listeners never need to hit the database to react. Side effects
 * (logging here, and anything added later such as notifying the
 * customer) hang off this event rather than the action.
 */
final readonly class InvoiceCreated
{
    public function __construct(
        public int $invoiceId,
        public string $invoiceNumber,
        public Money $total,
    ) {}
}
