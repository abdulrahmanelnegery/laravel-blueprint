<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * Input to App\Actions\CreateInvoice. The controller builds this from
 * the validated request so the action never sees an HTTP concept or an
 * anonymous array.
 */
final readonly class NewInvoice
{
    /**
     * @param  list<NewInvoiceLine>  $lines
     */
    public function __construct(
        public int $customerId,
        public string $currency,
        public array $lines,
    ) {}
}
