<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Base for recoverable business rule violations: expected outcomes of
 * valid looking input, such as an inactive customer or an invoice with
 * no lines. These map to HTTP 422 at the boundary (see bootstrap/app.php).
 *
 * Programming errors (a negative Money amount, a broken invariant) throw
 * SPL exceptions such as InvalidArgumentException instead and are not
 * translated here; they should surface as 500s and get fixed in code.
 */
abstract class DomainException extends RuntimeException
{
    /**
     * Stable, machine readable code for API clients to branch on.
     */
    abstract public function errorCode(): string;

    /**
     * Extra structured detail for the error payload and logs.
     *
     * @return array<string, scalar>
     */
    public function context(): array
    {
        return [];
    }
}
