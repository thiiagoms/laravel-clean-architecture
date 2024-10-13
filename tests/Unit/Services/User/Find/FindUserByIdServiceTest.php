<?php

namespace Tests\Unit\Services\User\Find;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Exceptions\LogicalException;
use App\Messages\System\SystemMessage;
use App\Models\User;
use App\Services\User\Find\FindUserByIdService;
use DomainException;
use Mockery;
use Tests\TestCase;

class FindUserByIdServiceTest extends TestCase
{
    public function testItShouldThrowLogicalExceptionWithInvalidParameterMessageWhenUserIsIsNotAValidUuid(): void
    {
        /** @var FindUserByIdService $findUserByIdService */
        $findUserByIdService = resolve(FindUserByIdService::class);

        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage(SystemMessage::INVALID_PARAMETER);

        $findUserByIdService->handle('invalid-uuid');
    }

    public function testItShouldThrowDomainExceptionWithNotFoundMessageWhenUserIdIsAValidUuidButDoesNotExist(): void
    {
        $userId = fake()->uuid();

        $userRepositoryMock = Mockery::mock(UserRepositoryContract::class);

        $userRepositoryMock->shouldReceive('find')
            ->once()
            ->with($userId)
            ->andReturnFalse();

        /** @var FindUserByIdService $findUserByIdService */
        $findUserByIdService = resolve(FindUserByIdService::class, ['userRepository' => $userRepositoryMock]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(SystemMessage::RESOURCE_NOT_FOUND);

        $findUserByIdService->handle($userId);

        Mockery::close();
    }

    public function testItShouldReturnUserWhenUserIdIsAValidUuidAndUserExistsInDatabase(): void
    {
        $userId = fake()->uuid();

        $userMock = new User(User::factory()->raw(['id' => $userId]));

        $useRepositoryMock = Mockery::mock(UserRepositoryContract::class);

        $useRepositoryMock->shouldReceive('find')
            ->once()
            ->with($userId)
            ->andReturn($userMock);

        /** @var FindUserByIdService $findUserByIdService */
        $findUserByIdService = resolve(FindUserByIdService::class, ['userRepository' => $useRepositoryMock]);

        $user = $findUserByIdService->handle($userId);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($user->toArray(), $userMock->toArray());
    }
}
