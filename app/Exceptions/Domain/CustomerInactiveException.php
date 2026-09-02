<?php

declare(strict_types=1);

namespace App\Exceptions\Domain;

use App\Exceptions\DomainException;

final class CustomerInactiveException extends DomainException
{
    public function __construct(public readonly int $customerId)
    {
        parent::__construct("Customer {$customerId} is not active and cannot be invoiced.");
    }

    public function errorCode(): string
    {
        return 'customer_inactive';
    }

    public function context(): array
    {
        return ['customer_id' => $this->customerId];
    }
}
