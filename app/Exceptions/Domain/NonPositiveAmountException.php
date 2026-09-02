<?php

declare(strict_types=1);

namespace App\Exceptions\Domain;

use App\Exceptions\DomainException;

final class NonPositiveAmountException extends DomainException
{
    public function __construct(
        public readonly string $lineDescription,
        public readonly int $minorUnits,
    ) {
        parent::__construct("Line \"{$lineDescription}\" must have a positive amount, got {$minorUnits}.");
    }

    public function errorCode(): string
    {
        return 'line_amount_not_positive';
    }

    public function context(): array
    {
        return [
            'line' => $this->lineDescription,
            'minor_units' => $this->minorUnits,
        ];
    }
}
