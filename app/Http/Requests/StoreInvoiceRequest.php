<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * HTTP shape validation only: types, presence, structure. The business
 * bounds (an invoice needs at least one line, amounts must be positive,
 * the customer must be active) live in App\Actions\CreateInvoice so they
 * stay enforceable and testable without an HTTP request. That split is
 * the whole point of this repository.
 */
final class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'currency' => ['required', 'string', 'size:3', 'alpha'],
            'lines' => ['present', 'array'],
            'lines.*.description' => ['required', 'string', 'max:255'],
            'lines.*.quantity' => ['required', 'integer', 'min:1'],
            'lines.*.unit_amount_minor_units' => ['required', 'integer'],
        ];
    }
}
