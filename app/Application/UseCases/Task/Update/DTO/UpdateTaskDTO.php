<?php

declare(strict_types=1);

namespace App\Application\UseCases\Task\Update\DTO;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\Task\Status\Status;
use App\Domain\Entity\Task\ValueObject\Description;
use App\Domain\Entity\Task\ValueObject\Title;
use App\Infrastructure\Support\Sanitizer;
use App\Presentation\Http\Api\V1\Task\Requests\Update\UpdateTaskApiRequest;

class UpdateTaskDTO
{
    public function __construct(
        private readonly Id $id,
        private readonly ?Title $title = null,
        private readonly ?Description $description = null,
        private readonly ?Status $status = null
    ) {}

    public function getId(): Id
    {
        return $this->id;
    }

    public function getTitle(): ?Title
    {
        return $this->title;
    }

    public function getDescription(): ?Description
    {
        return $this->description;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public static function fromRequest(UpdateTaskApiRequest $request, Id $id): self
    {
        $payload = Sanitizer::clean($request->validated());

        return new self(
            id: $id,
            title: ! empty($payload['title']) ? new Title($payload['title']) : null,
            description: ! empty($payload['description']) ? new Description($payload['description']) : null,
            status: ! empty($payload['status']) ? Status::from($payload['status']) : null
        );
    }
}
