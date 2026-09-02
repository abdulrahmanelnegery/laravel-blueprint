<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\InvoiceNumber;
use App\Models\Invoice;

/**
 * Persistence for the Invoice aggregate. Methods are named for intent,
 * return domain objects or models, and never leak a query builder.
 */
interface InvoiceRepository
{
    /**
     * Reserve the next sequential number for the given prefix and year.
     * Concurrency safe: the implementation takes a row lock.
     */
    public function nextNumber(string $prefix, int $year): InvoiceNumber;

    /**
     * Persist an unsaved Invoice together with its in memory lines, as
     * one transaction. Returns the invoice reloaded with relations.
     */
    public function save(Invoice $invoice): Invoice;

    public function findByNumber(string $number): ?Invoice;
}
