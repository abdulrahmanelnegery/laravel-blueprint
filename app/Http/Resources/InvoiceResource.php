<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Invoice
 */
final class InvoiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'number' => $this->number,
            'currency' => $this->currency,
            'status' => $this->status,
            'subtotal_minor_units' => $this->subtotal_minor_units,
            'issued_at' => $this->issued_at->toIso8601String(),
            'customer' => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
            ],
            'lines' => $this->lines
                ->map(static fn (InvoiceLine $line): array => [
                    'description' => $line->description,
                    'quantity' => $line->quantity,
                    'unit_amount_minor_units' => $line->unit_amount_minor_units,
                ])
                ->all(),
        ];
    }
}
