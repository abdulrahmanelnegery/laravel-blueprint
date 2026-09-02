<?php

declare(strict_types=1);

use App\Domain\Money;
use App\DTO\InvoiceLineSummary;
use App\DTO\InvoiceSummary;
use App\Rendering\MarkdownRenderer;
use App\Support\MoneyFormatter;

function summary(): InvoiceSummary
{
    return new InvoiceSummary(
        number: 'INV-2026-0007',
        customerName: 'Acme Widgets',
        currency: 'USD',
        lines: [
            new InvoiceLineSummary('Design work', 2, Money::of(5000, 'USD'), Money::of(10000, 'USD')),
            new InvoiceLineSummary('Hosting', 1, Money::of(1500, 'USD'), Money::of(1500, 'USD')),
        ],
        total: Money::of(11500, 'USD'),
    );
}

it('renders the header and billing line', function (): void {
    $out = (new MarkdownRenderer(new MoneyFormatter))->render(summary());

    expect($out)
        ->toContain('# Invoice INV-2026-0007')
        ->toContain('**Billed to:** Acme Widgets');
});

it('renders one table row per line with formatted money', function (): void {
    $out = (new MarkdownRenderer(new MoneyFormatter))->render(summary());

    expect($out)
        ->toContain('| 2 | Design work | $50.00 | $100.00 |')
        ->toContain('| 1 | Hosting | $15.00 | $15.00 |');
});

it('renders the total', function (): void {
    $out = (new MarkdownRenderer(new MoneyFormatter))->render(summary());

    expect($out)->toContain('**Total: $115.00**');
});
