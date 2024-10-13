<?php

declare(strict_types=1);

namespace App\Contracts\DTO\Task\Register;

use App\Contracts\DTO\BaseDTOContract;
use App\DTO\Task\Register\RegisterTaskDTO;
use App\Http\Requests\Task\Register\RegisterTaskRequest;

interface RegisterTaskDTOContract extends BaseDTOContract
{
    public static function fromRequest(RegisterTaskRequest $request): RegisterTaskDTO;

    public static function fromArray(array $payload): RegisterTaskDTO;
}
