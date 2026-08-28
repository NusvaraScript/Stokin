<?php

namespace App\Modules\Customer\Repositories;

use App\Infrastructure\Repositories\BaseRepository;
use App\Modules\Customer\Contracts\CustomerRepositoryInterface;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Override;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface {
    protected Model $model;

    public function __construct(Customer $model) {
        parent::__construct($model);
    }
}