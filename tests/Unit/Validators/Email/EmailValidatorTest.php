<?php

namespace Tests\Unit\Validators\Email;

use App\Exceptions\LogicalException;
use App\Messages\User\UserEmailMessage;
use App\Validators\Email\EmailValidator;
use PHPUnit\Framework\TestCase;

class EmailValidatorTest extends TestCase
{
    public function testItShoudlThrowLogicalExceptionWithInvalidEmailMessageWhenEmailProvidedisInvalid(): void
    {
        /** @var EmailValidator $emailValidator */
        $emailValidator = resolve(EmailValidator::class);

        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage(UserEmailMessage::emailIsInvalid());

        $emailValidator->checkEmailIsValid(fake()->name());
    }

    public function testItShouldReturnTrueWhenEmailProvidedIsValidEmail(): void
    {
        /** @var EmailValidator $emailValidator */
        $emailValidator = resolve(EmailValidator::class);

        $result = $emailValidator->checkEmailIsValid(fake()->freeEmail());

        $this->assertIsBool($result);
        $this->assertTrue($result);
    }
}
