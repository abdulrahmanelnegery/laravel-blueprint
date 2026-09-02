<?php

declare(strict_types=1);

namespace App\Rendering;

use App\DTO\InvoiceSummary;

/**
 * Renders an invoice summary to a string. Implementations differ only in
 * output format, never in what they pull from (they get a plain DTO).
 */
interface MessageRenderer
{
    public function render(InvoiceSummary $summary): string;
}
