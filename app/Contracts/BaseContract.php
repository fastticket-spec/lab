<?php

namespace App\Contracts;

interface BaseContract
{
    public function create(array $attributes): mixed;

    public function update(array $attributes, string $id): mixed;

    public function updateOrCreate(array $where, array $attributes);

    public function all($columns = ['*'], string $orderBy = 'id', string $sortBy = 'desc'): mixed;

    public function getBy(array $where, int $quantity = 3): mixed;

    public function find(string $id): mixed;

    public function findOneOrFail(string $id): mixed;

    public function findBy(array $data): mixed;

    public function findOneBy(array $data): mixed;

    public function findOneByOrFail(array $data): mixed;

    public function delete(string $id): mixed;

    public function count(): int;
}
