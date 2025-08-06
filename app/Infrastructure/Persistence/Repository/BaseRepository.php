<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    private function handle()
    {
        return app($this->model);
    }

    public function __construct()
    {
        $this->model = $this->handle();
    }
}
