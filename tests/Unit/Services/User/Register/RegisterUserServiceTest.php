<?php

namespace Tests\Unit\Services\User\Register;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Contracts\Validators\User\UserEmailValidatorContract;
use App\DTO\User\Register\RegisterUserDTO;
use App\Enums\User\UserRoleEnum;
use App\Exceptions\LogicalException;
use App\Messages\User\UserEmailMessage;
use App\Models\User;
use App\Services\User\Register\RegisterUserService;
use DomainException;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class RegisterUserServiceTest extends TestCase
{
    public function testItShoudlThrowLogicalExceptionWithInvalidEmailMessageWhenEmailProvidedisInvalid(): void
    {
        $registerUserDTO = RegisterUserDTO::fromArray([
            'name' => fake()->name(),
            'email' => fake()->name(),
            'password' => 'P4sSW0rd@!)_',
        ]);

        /** @var RegisterUserService $registerUserService */
        $registerUserService = resolve(RegisterUserService::class);

        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage(UserEmailMessage::emailIsInvalid());

        $registerUserService->handle($registerUserDTO);
    }

    public function testItShouldThrowDomainExceptionWithEmailAlreadyExistsMessageWhenEmailProvidedIsValidButAlreadyExists(): void
    {
        $registerUserDTO = RegisterUserDTO::fromArray([
            'name' => fake()->name(),
            'email' => fake()->freeEmail(),
            'password' => 'P4sSW0rd@!)_',
        ]);

        $userEmailValidatorMock = Mockery::mock(UserEmailValidatorContract::class);

        $userEmailValidatorMock->shouldReceive('checkUserEmailIsAvailable')
            ->once()
            ->with($registerUserDTO->email)
            ->andThrow(DomainException::class, UserEmailMessage::emailAlreadyExists());

        /** @var RegisterUserService $registerUserService */
        $registerUserService = resolve(RegisterUserService::class, ['userEmailValidator' => $userEmailValidatorMock]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(UserEmailMessage::emailAlreadyExists());

        $registerUserService->handle($registerUserDTO);

        Mockery::close();
    }

    public function testItShouldCreateUserWhenUserDataProvidedIsValidAndReturnCreateUserData(): void
    {
        $registerUserDTO = RegisterUserDTO::fromArray([
            'name' => fake()->name(),
            'email' => fake()->freeEmail(),
            'password' => 'P4sSW0rd@!)_',
        ]);

        $createdUserMock = new User([
            'id' => fake()->uuid(),
            'role' => UserRoleEnum::USER,
            ...$registerUserDTO->toArray(),
        ]);

        $userEmailValidatorMock = Mockery::mock(UserEmailValidatorContract::class);

        $userEmailValidatorMock->shouldReceive('checkUserEmailIsAvailable')
            ->once()
            ->with($registerUserDTO->email)
            ->andReturnNull();

        $userRepositoryMock = Mockery::mock(UserRepositoryContract::class);

        $userRepositoryMock->shouldReceive('create')
            ->once()
            ->with($registerUserDTO->toArray())
            ->andReturn($createdUserMock);

        /** @var RegisterUserService $registerUserService */
        $registerUserService = resolve(RegisterUserService::class, [
            'userEmailValidator' => $userEmailValidatorMock,
            'userRepository' => $userRepositoryMock,
        ]);

        $user = $registerUserService->handle($registerUserDTO);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($user->id, $createdUserMock->id);
        $this->assertEquals($user->name, $registerUserDTO->name);
        $this->assertEquals($user->email, $registerUserDTO->email);
        $this->assertEquals($user->role, $createdUserMock->role);
        $this->assertTrue(Hash::check($registerUserDTO->password, $user->password));

        Mockery::close();
    }
}
