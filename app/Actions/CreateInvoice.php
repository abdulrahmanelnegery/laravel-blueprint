<?php

declare(strict_types=1);

namespace App\Actions;

use App\Domain\Events\InvoiceCreated;
use App\Domain\Money;
use App\DTO\NewInvoice;
use App\Exceptions\Domain\CustomerInactiveException;
use App\Exceptions\Domain\CustomerNotFoundException;
use App\Exceptions\Domain\EmptyInvoiceException;
use App\Exceptions\Domain\NonPositiveAmountException;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Repositories\CustomerRepository;
use App\Repositories\InvoiceRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Date;

/**
 * The one write path for creating an invoice. Holds the business rules
 * that the HTTP layer deliberately does not: at least one line, every
 * line amount positive, the customer active. Depends on repository
 * interfaces and an event dispatcher, nothing framework specific beyond
 * that.
 */
final readonly class CreateInvoice
{
    private const NUMBER_PREFIX = 'INV';

    private const INITIAL_STATUS = 'draft';

    public function __construct(
        private InvoiceRepository $invoices,
        private CustomerRepository $customers,
        private Dispatcher $events,
    ) {}

    public function __invoke(NewInvoice $data): Invoice
    {
        if ($data->lines === []) {
            throw new EmptyInvoiceException;
        }

        $customer = $this->customers->find($data->customerId)
            ?? throw new CustomerNotFoundException($data->customerId);

        if (! $customer->is_active) {
            throw new CustomerInactiveException($customer->id);
        }

        $currency = strtoupper($data->currency);
        $subtotal = Money::zero($currency);

        /** @var list<InvoiceLine> $lineModels */
        $lineModels = [];

        foreach ($data->lines as $line) {
            if ($line->unitAmountMinorUnits <= 0) {
                throw new NonPositiveAmountException($line->description, $line->unitAmountMinorUnits);
            }

            $unitPrice = Money::of($line->unitAmountMinorUnits, $currency);
            $subtotal = $subtotal->add($unitPrice->multiply($line->quantity));

            $lineModels[] = new InvoiceLine([
                'description' => $line->description,
                'quantity' => $line->quantity,
                'unit_amount_minor_units' => $line->unitAmountMinorUnits,
                'currency' => $currency,
            ]);
        }

        $issuedAt = Date::now();
        $number = $this->invoices->nextNumber(self::NUMBER_PREFIX, (int) $issuedAt->format('Y'));

        $invoice = new Invoice([
            'customer_id' => $customer->id,
            'number' => $number->toString(),
            'currency' => $currency,
            'subtotal_minor_units' => $subtotal->minorUnits,
            'status' => self::INITIAL_STATUS,
            'issued_at' => $issuedAt,
        ]);
        $invoice->setRelation('lines', collect($lineModels));

        $invoice = $this->invoices->save($invoice);

        $this->events->dispatch(new InvoiceCreated(
            invoiceId: $invoice->id,
            invoiceNumber: $invoice->number,
            total: $subtotal,
        ));

        return $invoice;
    }
}
