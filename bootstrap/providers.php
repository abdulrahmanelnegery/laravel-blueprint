<?php

declare(strict_types=1);
use App\Providers\AppServiceProvider;
use App\Providers\RepositoryServiceProvider;

return [
    AppServiceProvider::class,
    // App\Providers\EventServiceProvider is registered automatically by the
    // framework (Application::configure()->withEvents()); listing it here as
    // well would boot it twice and double register every listener.
    RepositoryServiceProvider::class,
];
