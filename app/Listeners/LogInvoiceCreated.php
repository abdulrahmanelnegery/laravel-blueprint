<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Domain\Events\InvoiceCreated;
use Psr\Log\LoggerInterface;

/**
 * Decoupled side effect: write an audit line when an invoice is created.
 * Wired to the event in App\Providers\EventServiceProvider.
 */
final readonly class LogInvoiceCreated
{
    public function __construct(private LoggerInterface $log) {}

    public function handle(InvoiceCreated $event): void
    {
        $this->log->info('invoice.created', [
            'invoice_id' => $event->invoiceId,
            'invoice_number' => $event->invoiceNumber,
            'total_minor_units' => $event->total->minorUnits,
            'currency' => $event->total->currency,
        ]);
    }
}
