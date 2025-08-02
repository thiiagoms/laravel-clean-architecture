<?php

declare(strict_types=1);

namespace App\Application\UseCases\Task\Register;

use App\Application\UseCases\Task\Register\DTO\RegisterTaskDTO;
use App\Application\UseCases\Task\Register\Service\RegisterTaskService;
use App\Application\UseCases\User\Common\Service\FindOrFailUserByIdService;
use App\Domain\Entity\Task\Factory\TaskFactory;
use App\Domain\Entity\Task\Task;

readonly class RegisterTask
{
    public function __construct(
        private RegisterTaskService $registerTaskService,
        private FindOrFailUserByIdService $findOrFailUserByIdService
    ) {}

    public function handle(RegisterTaskDTO $dto): Task
    {
        $user = $this->findOrFailUserByIdService->findOrFail($dto->getUserId());

        $task = TaskFactory::create(title: $dto->getTitle(), description: $dto->getDescription(), owner: $user);

        return $this->registerTaskService->handle($task);
    }
}
