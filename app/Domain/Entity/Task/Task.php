<?php

declare(strict_types=1);

namespace App\Domain\Entity\Task;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Entity\Task\Interface\TaskOwnerInterface;
use App\Domain\Entity\Task\Status\Interface\StatusInterface;
use App\Domain\Entity\Task\Status\Status;
use App\Domain\Entity\Task\ValueObject\Description;
use App\Domain\Entity\Task\ValueObject\Title;
use DateTimeImmutable;

class Task
{
    private readonly DateTimeImmutable $createdAt;

    private DateTimeImmutable $updatedAt;

    public function __construct(
        private Title $title,
        private Description $description,
        private readonly TaskOwnerInterface $owner,
        private StatusInterface $status,
        private readonly ?Id $id = null,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
    ) {
        $now = new DateTimeImmutable;

        $this->createdAt = $createdAt ?? $now;
        $this->updatedAt = $updatedAt ?? $now;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function getDescription(): Description
    {
        return $this->description;
    }

    public function getOwner(): TaskOwnerInterface
    {
        return $this->owner;
    }

    public function getStatus(): Status
    {
        return $this->status->getStatus();
    }

    public function getId(): ?Id
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function changeTitleTo(Title $title): void
    {
        $this->title = $title;
        $this->touch();
    }

    public function changeDescriptionTo(Description $description): void
    {
        $this->description = $description;
        $this->touch();
    }

    public function todo(): void
    {
        $this->status->todo($this);
        $this->touch();
    }

    public function doing(): void
    {
        $this->status->doing($this);
        $this->touch();
    }

    public function done(): void
    {
        $this->status->done($this);
        $this->touch();
    }

    public function cancelled(): void
    {
        $this->status->cancelled($this);
        $this->touch();
    }

    /**
     * @internal Use only within status transition methods
     * // TODO: Consider making this private and using a dedicated status transition service
     */
    public function setStatus(StatusInterface $status): void
    {
        $this->status = $status;
        $this->touch();
    }

    private function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable;
    }
}
