<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Events\InvoiceCreated;
use App\Listeners\LogInvoiceCreated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Explicit event to listener wiring. Kept explicit (rather than relying
 * on auto discovery) so the side effects of a domain event are readable
 * in one place.
 */
final class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, list<class-string>>
     */
    protected $listen = [
        InvoiceCreated::class => [
            LogInvoiceCreated::class,
        ],
    ];
}
