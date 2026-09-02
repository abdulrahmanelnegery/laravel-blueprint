<?php

declare(strict_types=1);

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
