<?php

namespace App\Infrastructure\Persistence\Mapper\Task;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\Task\Interface\TaskOwnerInterface;
use App\Domain\Entity\Task\Status\Factory\StatusFactory;
use App\Domain\Entity\Task\Status\Status;
use App\Domain\Entity\Task\Task as DomainTask;
use App\Domain\Entity\Task\ValueObject\Description;
use App\Domain\Entity\Task\ValueObject\Title;
use App\Infrastructure\Persistence\Model\Task as EloquentTask;

class TaskMapper
{
    public static function toDomain(EloquentTask $model, TaskOwnerInterface $owner): DomainTask
    {
        return new DomainTask(
            title: new Title($model->title),
            description: new Description($model->description),
            owner: $owner,
            status: StatusFactory::map(Status::from($model->status)),
            id: new Id($model->id),
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable()
        );
    }

    public static function toPersistence(DomainTask $task): array
    {
        return [
            'id' => $task->getId()?->getValue(),
            'user_id' => $task->getOwner()->getId()->getValue(),
            'title' => $task->getTitle()->getValue(),
            'description' => $task->getDescription()->getValue(),
            'status' => $task->getStatus()->value,
        ];
    }
}
