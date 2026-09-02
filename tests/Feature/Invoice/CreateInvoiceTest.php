<?php

declare(strict_types=1);

use App\Models\Customer;
use Illuminate\Support\Facades\Log;

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function invoicePayload(Customer $customer, array $overrides = []): array
{
    return array_merge([
        'customer_id' => $customer->id,
        'currency' => 'USD',
        'lines' => [
            ['description' => 'Design work', 'quantity' => 2, 'unit_amount_minor_units' => 5000],
            ['description' => 'Hosting', 'quantity' => 1, 'unit_amount_minor_units' => 1500],
        ],
    ], $overrides);
}

it('creates an invoice and returns 201 with the assembled resource', function (): void {
    $customer = Customer::factory()->create();

    $response = $this->postJson('/api/invoices', invoicePayload($customer));

    $response->assertCreated()
        ->assertJsonPath('data.number', 'INV-'.date('Y').'-0001')
        ->assertJsonPath('data.currency', 'USD')
        ->assertJsonPath('data.status', 'draft')
        ->assertJsonPath('data.subtotal_minor_units', 11500)
        ->assertJsonPath('data.customer.name', $customer->name)
        ->assertJsonCount(2, 'data.lines');

    $this->assertDatabaseHas('invoices', [
        'number' => 'INV-'.date('Y').'-0001',
        'subtotal_minor_units' => 11500,
    ]);
    $this->assertDatabaseCount('invoice_lines', 2);
});

it('allocates sequential invoice numbers within the year', function (): void {
    $customer = Customer::factory()->create();

    $this->postJson('/api/invoices', invoicePayload($customer))->assertCreated();

    $this->postJson('/api/invoices', invoicePayload($customer))
        ->assertCreated()
        ->assertJsonPath('data.number', 'INV-'.date('Y').'-0002');
});

it('dispatches InvoiceCreated so the logging listener records an audit line', function (): void {
    Log::spy();
    $customer = Customer::factory()->create();

    $this->postJson('/api/invoices', invoicePayload($customer))->assertCreated();

    Log::shouldHaveReceived('info')
        ->withArgs(
            fn (string $message, array $context): bool => $message === 'invoice.created'
                && $context['total_minor_units'] === 11500
                && $context['currency'] === 'USD',
        )
        ->once();
});

it('rejects an invoice with no lines as a business rule violation', function (): void {
    $customer = Customer::factory()->create();

    $this->postJson('/api/invoices', invoicePayload($customer, ['lines' => []]))
        ->assertStatus(422)
        ->assertJsonPath('error.code', 'invoice_empty');
});

it('rejects a non positive line amount as a business rule violation', function (): void {
    $customer = Customer::factory()->create();

    $payload = invoicePayload($customer, ['lines' => [
        ['description' => 'Bad line', 'quantity' => 1, 'unit_amount_minor_units' => 0],
    ]]);

    $this->postJson('/api/invoices', $payload)
        ->assertStatus(422)
        ->assertJsonPath('error.code', 'line_amount_not_positive');
});

it('rejects an inactive customer as a business rule violation', function (): void {
    $customer = Customer::factory()->inactive()->create();

    $this->postJson('/api/invoices', invoicePayload($customer))
        ->assertStatus(422)
        ->assertJsonPath('error.code', 'customer_inactive');
});

it('rejects a malformed payload at the HTTP boundary before the action runs', function (): void {
    $customer = Customer::factory()->create();

    $this->postJson('/api/invoices', invoicePayload($customer, ['currency' => 'US']))
        ->assertStatus(422)
        ->assertJsonValidationErrors('currency');
});
