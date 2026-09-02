<?php

declare(strict_types=1);

namespace App\Rendering;

use App\DTO\InvoiceSummary;
use App\Support\MoneyFormatter;

final readonly class MarkdownRenderer implements MessageRenderer
{
    public function __construct(private MoneyFormatter $money) {}

    public function render(InvoiceSummary $summary): string
    {
        $rows = [
            "# Invoice {$summary->number}",
            '',
            "**Billed to:** {$summary->customerName}",
            '',
            '| Qty | Description | Unit | Amount |',
            '| --: | :--- | ---: | ---: |',
        ];

        foreach ($summary->lines as $line) {
            $rows[] = sprintf(
                '| %d | %s | %s | %s |',
                $line->quantity,
                $line->description,
                $this->money->format($line->unitPrice),
                $this->money->format($line->lineTotal),
            );
        }

        $rows[] = '';
        $rows[] = sprintf('**Total: %s**', $this->money->format($summary->total));

        return implode("\n", $rows);
    }
}
