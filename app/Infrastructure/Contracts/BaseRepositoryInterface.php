<?php

namespace App\Infrastructure\Contracts;

interface BaseRepositoryInterface
{
    public function all(): array;
    public function findById(int $id): ?object;
    public function findByEmail(string $email): ?object;
    public function create(array $data): object;
    public function update(int $id, array $data): ?object;
    public function delete(int $id): bool;
}