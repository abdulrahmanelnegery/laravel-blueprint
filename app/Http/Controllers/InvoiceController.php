<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateInvoice;
use App\DTO\NewInvoice;
use App\DTO\NewInvoiceLine;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Thin by design: validate the request shape, hand a DTO to the action,
 * return a resource. No business logic, no Eloquent, no error handling
 * (domain exceptions are mapped centrally in bootstrap/app.php).
 */
final class InvoiceController extends Controller
{
    public function store(StoreInvoiceRequest $request, CreateInvoice $createInvoice): JsonResponse
    {
        $invoice = $createInvoice($this->toDto($request));

        return InvoiceResource::make($invoice)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    private function toDto(StoreInvoiceRequest $request): NewInvoice
    {
        /**
         * @var array{
         *     customer_id: int,
         *     currency: string,
         *     lines: list<array{description: string, quantity: int, unit_amount_minor_units: int}>
         * } $data
         */
        $data = $request->validated();

        $lines = array_map(
            static fn (array $line): NewInvoiceLine => new NewInvoiceLine(
                description: $line['description'],
                quantity: $line['quantity'],
                unitAmountMinorUnits: $line['unit_amount_minor_units'],
            ),
            $data['lines'],
        );

        return new NewInvoice(
            customerId: $data['customer_id'],
            currency: $data['currency'],
            lines: $lines,
        );
    }
}
