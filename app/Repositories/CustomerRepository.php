<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Customer;

/**
 * Read access to customers for the application layer. The interface
 * lives here in the domain facing package; the Eloquent implementation
 * lives under Repositories\Eloquent and is bound in
 * App\Providers\RepositoryServiceProvider.
 */
interface CustomerRepository
{
    public function find(int $id): ?Customer;
}
