<?php

namespace Tests\Unit\Domain\Entity\User\ValueObject;

use App\Domain\Entity\User\ValueObject\Password;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    public static function invalidPasswordCases(): array
    {
        return [
            'it should throw exception when provided password is empty' => [
                '',
            ],
            'it should throw exception when provided password length is less than 8' => [
                'short1!',
            ],
            'it should throw exception when provided password does not contain at least one uppercase letter' => [
                'lowercase1!',
            ],
            'it should throw exception when provided password does not contain at least one lowercase letter' => [
                'UPPERCASE1!',
            ],
            'it should throw exception when provided password does not contain at least one digit' => [
                'Uppercase!',
            ],
            'it should throw exception when provided password does not contain at least one special character' => [
                'Uppercase1',
            ],
        ];
    }

    #[Test]
    #[DataProvider('invalidPasswordCases')]
    public function itShouldThrowExceptionWhenPasswordIsInvalid(string $password): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one digit, and one special character.');

        new Password($password);
    }

    #[Test]
    public function itShouldCreatePasswordWhenPasswordIsValid(): void
    {
        $password = new Password('ValidPassword1!');

        $this->assertIsString($password->getValue());
        $this->assertNotEquals('ValidPassword1!', $password->getValue());
        $this->assertTrue($password->match('ValidPassword1!'));
    }

    #[Test]
    public function itShouldCreatePasswordWhenProvidedPasswordIsValidAndNotHashed(): void
    {
        $password = new Password('ValidPassword1!', false);

        $this->assertSame('ValidPassword1!', $password->getValue());
        $this->assertFalse($password->match('ValidPassword1!'));
    }

    #[Test]
    public function itShouldReturnFalseWhenPasswordDoeNotMatch(): void
    {
        $password = new Password('ValidPassword1!');

        $this->assertFalse($password->match('InvalidPassword1!'));
    }
}
