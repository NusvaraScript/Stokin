<?php

namespace App\Modules\Auth\Repositories;

use App\Infrastructure\Repositories\BaseRepository;
use App\Models\User;
use App\Modules\Auth\Contracts\AuthRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class AuthRepository extends BaseRepository implements AuthRepositoryInterface {
    protected Model $model;

    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
