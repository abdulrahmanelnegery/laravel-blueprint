<?php

declare(strict_types=1);

namespace App\Exceptions\Domain;

use App\Exceptions\DomainException;

final class EmptyInvoiceException extends DomainException
{
    public function __construct()
    {
        parent::__construct('An invoice must have at least one line.');
    }

    public function errorCode(): string
    {
        return 'invoice_empty';
    }
}
