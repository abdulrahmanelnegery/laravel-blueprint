<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Money;
use Carbon\CarbonImmutable;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $customer_id
 * @property string $number
 * @property string $currency
 * @property int $subtotal_minor_units
 * @property string $status
 * @property CarbonImmutable $issued_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Customer $customer
 * @property-read Collection<int, InvoiceLine> $lines
 */
final class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'customer_id',
        'number',
        'currency',
        'subtotal_minor_units',
        'status',
        'issued_at',
    ];

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return HasMany<InvoiceLine, $this>
     */
    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    public function total(): Money
    {
        return Money::of($this->subtotal_minor_units, $this->currency);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal_minor_units' => 'integer',
            'issued_at' => 'immutable_datetime',
        ];
    }
}
