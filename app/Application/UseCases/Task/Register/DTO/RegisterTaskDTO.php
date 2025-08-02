<?php

declare(strict_types=1);

namespace App\Application\UseCases\Task\Register\DTO;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\Task\ValueObject\Description;
use App\Domain\Entity\Task\ValueObject\Title;
use App\Infrastructure\Support\Sanitizer;
use App\Presentation\Http\Api\V1\Task\Requests\Register\RegisterTaskApiRequest;

class RegisterTaskDTO
{
    public function __construct(
        private readonly Title $title,
        private readonly Description $description,
        private readonly Id $userId
    ) {}

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function getDescription(): Description
    {
        return $this->description;
    }

    public function getUserId(): Id
    {
        return $this->userId;
    }

    public static function fromRequest(RegisterTaskApiRequest $request): self
    {
        $payload = Sanitizer::clean($request->validated());

        $payload['user_id'] = $request->user('api')->id;

        return new self(
            title: new Title($payload['title']),
            description: new Description($payload['description']),
            userId: new Id($payload['user_id'])
        );
    }
}
