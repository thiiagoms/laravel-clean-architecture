<?php

namespace Tests\Unit\Domain\Entity\User\ValueObject;

use App\Domain\Entity\User\ValueObject\Email;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public static function invalidEmailCases(): array
    {
        return [
            'should throw exception when provided email is not a valid email address' => [
                'invalid-email',
            ],
            'should throw exception when provided email is a empty string' => [
                '',
            ],
        ];
    }

    #[Test]
    #[DataProvider('invalidEmailCases')]
    public function it_should_throw_exception_for_invalid_email(string $email): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid e-mail address given: '{$email}'");

        new Email($email);
    }

    #[Test]
    public function it_should_create_email_with_valid_email(): void
    {
        $email = new Email('ilovelaravel@gmail.com');

        $this->assertSame('ilovelaravel@gmail.com', $email->getValue());
    }

    public static function emailEqualityCases(): array
    {
        return [
            [new Email('ilovelaravel@gmail.com'), true],
            [new Email('ilovephp@gmail.com'), false],
        ];
    }

    #[Test]
    #[DataProvider('emailEqualityCases')]
    public function it_should_validate_email_matches(Email $email, bool $emailMatch): void
    {
        $emailValueObject = new Email('ilovelaravel@gmail.com');

        $this->assertSame($emailMatch, $emailValueObject->equals($email));
    }
}
