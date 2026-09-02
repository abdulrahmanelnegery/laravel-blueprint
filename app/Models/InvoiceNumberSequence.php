<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Per (prefix, year) counter used to hand out sequential invoice
 * numbers. Written only through EloquentInvoiceRepository::nextNumber,
 * which takes a row lock while it increments.
 *
 * @property int $id
 * @property string $prefix
 * @property int $year
 * @property int $last_sequence
 */
final class InvoiceNumberSequence extends Model
{
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'prefix',
        'year',
        'last_sequence',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'last_sequence' => 'integer',
        ];
    }
}
