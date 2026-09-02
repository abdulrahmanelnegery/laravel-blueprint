<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\CustomerRepository;

final class EloquentCustomerRepository implements CustomerRepository
{
    public function find(int $id): ?Customer
    {
        return Customer::query()->find($id);
    }
}
