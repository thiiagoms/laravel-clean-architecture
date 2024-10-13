<?php

namespace Tests\Unit\Services\User\Find;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Exceptions\LogicalException;
use App\Messages\User\UserEmailMessage;
use App\Models\User;
use App\Services\User\Find\FindUserByEmailService;
use Mockery;
use Tests\TestCase;

class FindUserByEmailServiceTest extends TestCase
{
    public function testItShouldThrowLogicalExceptionWithInvalidEmailMessageWhenEmailProvidedIsInvalid(): void
    {
        /** @var FindUserByEmailService $findUserByEmailService */
        $findUserByEmailService = resolve(FindUserByEmailService::class);

        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage(UserEmailMessage::emailIsInvalid());

        $findUserByEmailService->handle(fake()->name());
    }

    public function testItShouldReturnFalseWhenEmailProvidedIsValidButDoesNotExistInDatabase(): void
    {
        $email = fake()->freeEmail();

        $userRepositoryMock = Mockery::mock(UserRepositoryContract::class);

        $userRepositoryMock->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturnFalse();

        /** @var FindUserByEmailService $findUserByEmailService */
        $findUserByEmailService = resolve(FindUserByEmailService::class, ['userRepository' => $userRepositoryMock]);

        $result = $findUserByEmailService->handle($email);

        $this->assertIsBool($result);
        $this->assertFalse($result);

        Mockery::close();
    }

    public function testItShouldReturnUserWhenEmailProvidedIsValidAndExistsInDatabase(): void
    {
        $email = fake()->freeEmail();

        $userWithEmailMock = new User(User::factory()->raw(['email' => $email]));

        $userRepositoryMock = Mockery::mock(UserRepositoryContract::class);

        $userRepositoryMock->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn($userWithEmailMock);

        /** @var FindUserByEmailService $findUserByEmailService */
        $findUserByEmailService = resolve(FindUserByEmailService::class, ['userRepository' => $userRepositoryMock]);

        $result = $findUserByEmailService->handle($email);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($userWithEmailMock, $result);

        Mockery::close();
    }
}
