<?php

declare(strict_types=1);

namespace App\Domain\Entity\Task\Status\Factory;

use App\Domain\Entity\Task\Status\implementation\Cancelled;
use App\Domain\Entity\Task\Status\implementation\Doing;
use App\Domain\Entity\Task\Status\implementation\Done;
use App\Domain\Entity\Task\Status\implementation\Todo;
use App\Domain\Entity\Task\Status\Interface\StatusInterface;
use App\Domain\Entity\Task\Status\Status;

abstract class StatusFactory
{
    public static function map(Status $status): StatusInterface
    {
        return match ($status) {
            Status::TODO => new Todo,
            Status::DOING => new Doing,
            Status::DONE => new Done,
            Status::CANCELLED => new Cancelled,
        };
    }
}
