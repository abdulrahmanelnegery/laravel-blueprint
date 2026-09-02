<?php

declare(strict_types=1);

namespace App\Exceptions\Domain;

use App\Exceptions\DomainException;

final class CurrencyMismatchException extends DomainException
{
    public function __construct(
        public readonly string $expected,
        public readonly string $actual,
    ) {
        parent::__construct("Currency mismatch: expected {$expected}, got {$actual}.");
    }

    public function errorCode(): string
    {
        return 'currency_mismatch';
    }

    public function context(): array
    {
        return [
            'expected' => $this->expected,
            'actual' => $this->actual,
        ];
    }
}
