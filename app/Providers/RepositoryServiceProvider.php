<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\CustomerRepository;
use App\Repositories\Eloquent\EloquentCustomerRepository;
use App\Repositories\Eloquent\EloquentInvoiceRepository;
use App\Repositories\InvoiceRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Binds repository interfaces to their Eloquent implementations. The
 * application and domain layers only ever type hint the interface.
 */
final class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        InvoiceRepository::class => EloquentInvoiceRepository::class,
        CustomerRepository::class => EloquentCustomerRepository::class,
    ];
}
