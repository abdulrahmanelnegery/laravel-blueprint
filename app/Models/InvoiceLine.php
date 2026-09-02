<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $invoice_id
 * @property string $description
 * @property int $quantity
 * @property int $unit_amount_minor_units
 * @property string $currency
 * @property-read Invoice $invoice
 */
final class InvoiceLine extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'description',
        'quantity',
        'unit_amount_minor_units',
        'currency',
    ];

    /**
     * @return BelongsTo<Invoice, $this>
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function unitPrice(): Money
    {
        return Money::of($this->unit_amount_minor_units, $this->currency);
    }

    public function lineTotal(): Money
    {
        return $this->unitPrice()->multiply($this->quantity);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_amount_minor_units' => 'integer',
        ];
    }
}
