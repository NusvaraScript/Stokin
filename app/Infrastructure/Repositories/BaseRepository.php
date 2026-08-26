<?php

namespace App\Infrastructure\Repositories;

use App\Infrastructure\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function all(): array {
        return $this->model->all()->toArray();
    }

    public function findById(int $id): ?object {
        return $this->model->find($id);
    }

    public function findByEmail(string $email): ?object {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data): object {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?object {
        $model = $this->findById($id);
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }

    public function delete(int $id): bool {
        $model = $this->findById($id);
        if ($model) {
            return $model->delete();
        }
        return false;
    }
}