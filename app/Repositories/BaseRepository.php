<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository
{
    protected $model;

    private function handle(): mixed
    {
        return app($this->model);
    }

    public function __construct()
    {
        $this->model = $this->handle();
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(string $id): mixed
    {
        $result = $this->model->find($id);

        return ! is_null($result) ? $result : false;
    }

    public function create(array $params): mixed
    {
        return $this->model->create($params);
    }

    public function update(string $id, array $params): bool
    {
        return $this->model->find($id)?->update($params);
    }

    public function destroy(string $id): bool
    {
        return (bool) $this->model->destroy($id);
    }
}
