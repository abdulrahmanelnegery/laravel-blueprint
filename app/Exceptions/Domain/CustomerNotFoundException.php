<?php

declare(strict_types=1);

namespace App\Exceptions\Domain;

use App\Exceptions\DomainException;

final class CustomerNotFoundException extends DomainException
{
    public function __construct(public readonly int $customerId)
    {
        parent::__construct("Customer {$customerId} does not exist.");
    }

    public function errorCode(): string
    {
        return 'customer_not_found';
    }

    public function context(): array
    {
        return ['customer_id' => $this->customerId];
    }
}
