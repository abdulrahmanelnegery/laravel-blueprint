<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * Present for completeness and model tests. The production write path is
 * App\Actions\CreateInvoice, which does not use factories.
 *
 * @extends Factory<Invoice>
 */
final class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'number' => sprintf('INV-%s-%04d', Date::now()->format('Y'), fake()->unique()->numberBetween(1, 9999)),
            'currency' => 'USD',
            'subtotal_minor_units' => fake()->numberBetween(1000, 500000),
            'status' => 'draft',
            'issued_at' => Date::now(),
        ];
    }
}
