<?php

namespace App\Modules\Auth\Repositories;

use App\Infrastructure\Repositories\BaseRepository;
use App\Modules\Auth\Contracts\AuthRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Override;

abstract class AuthRepository extends BaseRepository implements AuthRepositoryInterface {
    protected Model $model;

    public function __construct(Model $model) {
        parent::__construct($model);
    }
}
