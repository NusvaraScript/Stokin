<?php

namespace App\Modules\User\Repositories;

use App\Infrastructure\Repositories\BaseRepository;
use App\Modules\User\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Override;

class UserRepository extends BaseRepository implements UserRepositoryInterface {
    protected Model $model;

    public function __construct(User $model) {
        parent::__construct($model);
    }
}