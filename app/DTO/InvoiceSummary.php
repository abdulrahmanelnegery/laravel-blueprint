<?php

declare(strict_types=1);

namespace App\DTO;

use App\Domain\Money;
use App\Models\Invoice;
use App\Models\InvoiceLine;

/**
 * The read model handed to a MessageRenderer. Assembled from an Invoice
 * aggregate once, so renderers stay free of persistence and framework
 * concerns.
 */
final readonly class InvoiceSummary
{
    /**
     * @param  array<int, InvoiceLineSummary>  $lines
     */
    public function __construct(
        public string $number,
        public string $customerName,
        public string $currency,
        public array $lines,
        public Money $total,
    ) {}

    public static function fromModel(Invoice $invoice): self
    {
        $lines = $invoice->lines
            ->map(static fn (InvoiceLine $line): InvoiceLineSummary => new InvoiceLineSummary(
                description: $line->description,
                quantity: $line->quantity,
                unitPrice: $line->unitPrice(),
                lineTotal: $line->lineTotal(),
            ))
            ->values()
            ->all();

        return new self(
            number: $invoice->number,
            customerName: $invoice->customer->name,
            currency: $invoice->currency,
            lines: $lines,
            total: $invoice->total(),
        );
    }
}
