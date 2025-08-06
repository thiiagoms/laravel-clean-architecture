<?php

namespace Tests\Unit\Application\UseCases\Task\Destroy;

use App\Application\UseCases\Task\Destroy\DestroyTask;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Repository\Task\Destroy\DestroyTaskRepositoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DestroyTaskTest extends TestCase
{
    private DestroyTaskRepositoryInterface|MockObject $repository;

    private DestroyTask $destroyTask;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->repository = $this->createMock(DestroyTaskRepositoryInterface::class);

        $this->destroyTask = new DestroyTask($this->repository);
    }

    #[Test]
    public function it_should_return_true_when_task_is_destroyed(): void
    {
        $id = new Id(fake()->uuid());

        $this->repository
            ->expects($this->once())
            ->method('destroy')
            ->with($id)
            ->willReturn(true);

        $result = $this->destroyTask->handle($id);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_should_return_false_when_task_is_not_destroyed(): void
    {
        $id = new Id(fake()->uuid());

        $this->repository
            ->expects($this->once())
            ->method('destroy')
            ->with($id)
            ->willReturn(false);

        $result = $this->destroyTask->handle($id);

        $this->assertFalse($result);
    }
}
