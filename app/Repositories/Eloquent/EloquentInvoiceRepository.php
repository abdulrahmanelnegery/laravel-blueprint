<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Domain\InvoiceNumber;
use App\Models\Invoice;
use App\Models\InvoiceNumberSequence;
use App\Repositories\InvoiceRepository;
use Illuminate\Support\Facades\DB;

final class EloquentInvoiceRepository implements InvoiceRepository
{
    public function nextNumber(string $prefix, int $year): InvoiceNumber
    {
        $prefix = strtoupper($prefix);

        return DB::transaction(function () use ($prefix, $year): InvoiceNumber {
            $sequence = InvoiceNumberSequence::query()
                ->where('prefix', $prefix)
                ->where('year', $year)
                ->lockForUpdate()
                ->first()
                ?? new InvoiceNumberSequence([
                    'prefix' => $prefix,
                    'year' => $year,
                    'last_sequence' => 0,
                ]);

            $sequence->last_sequence++;
            $sequence->save();

            return InvoiceNumber::fromParts($prefix, $year, $sequence->last_sequence);
        });
    }

    public function save(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice): Invoice {
            $lines = $invoice->lines;

            $invoice->save();
            $invoice->lines()->saveMany($lines);

            return $invoice->load(['customer', 'lines']);
        });
    }

    public function findByNumber(string $number): ?Invoice
    {
        return Invoice::query()
            ->with(['customer', 'lines'])
            ->where('number', $number)
            ->first();
    }
}
