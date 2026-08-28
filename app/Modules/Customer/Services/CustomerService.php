<?php

namespace App\Modules\Customer\Services;

use App\Modules\Customer\Contracts\CustomerRepositoryInterface;

class CustomerService
{
    protected CustomerRepositoryInterface $CustomerRepository;

    public function __construct(CustomerRepositoryInterface $CustomerRepository)
    {
        $this->CustomerRepository = $CustomerRepository;
    }
}