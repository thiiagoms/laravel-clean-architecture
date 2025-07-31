<?php

namespace Tests\Unit\Validators\User;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Exceptions\LogicalException;
use App\Infrastructure\Persistence\Model\User;
use App\Messages\User\UserEmailMessage;
use App\Validators\User\UserEmailValidator;
use DomainException;
use Mockery;
use Tests\TestCase;

class UserEmailValidatorTest extends TestCase
{
    public function testItShoudlThrowLogicalExceptionWithInvalidEmailMessageWhenEmailProvidedisInvalid(): void
    {
        /** @var UserEmailValidator $userEmailValidator */
        $userEmailValidator = resolve(UserEmailValidator::class);

        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage(UserEmailMessage::emailIsInvalid());

        $userEmailValidator->checkUserEmailIsAvailable(fake()->name());
    }

    public function testItShouldThrowDomainExceptionWithEmailAlreadyExistsMessageWhenEmailProvidedIsValidButAlreadyExists(): void
    {
        $email = 'testesocial@gmail.com';

        $userWithEmailMock = new User(['email' => $email]);

        $userRepositoryMock = Mockery::mock(UserRepositoryContract::class);

        $userRepositoryMock->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn($userWithEmailMock);

        /** @var UserEmailValidator $userEmailValidator */
        $userEmailValidator = resolve(UserEmailValidator::class, ['userRepository' => $userRepositoryMock]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(UserEmailMessage::emailAlreadyExists());

        $userEmailValidator->checkUserEmailIsAvailable($email);

        Mockery::close();
    }

    public function testItShouldReturnNullWhenEmailProvidedisValidAndDoesNotExistsInDatabase(): void
    {
        $this->expectNotToPerformAssertions();

        $email = fake()->freeEmail();

        $userRepositoryMock = Mockery::mock(UserRepositoryContract::class);

        $userRepositoryMock->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturnFalse();

        /** @var UserEmailValidator $userEmailValidator */
        $userEmailValidator = resolve(UserEmailValidator::class, ['userRepository' => $userRepositoryMock]);

        $userEmailValidator->checkUserEmailIsAvailable($email);

        Mockery::close();
    }
}
