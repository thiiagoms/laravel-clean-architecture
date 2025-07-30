<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    public static function isEmailHelperProvider(): array
    {
        return [
            'should return true if email is valid' => [
                'email' => fake()->freeEmail(),
                'result' => true,
            ],
            'should return false if email is not valid' => [
                'email' => fake()->name(),
                'result' => false,
            ],
            'should return false if email is empty' => [
                'email' => '',
                'result' => false,
            ],
        ];
    }

    #[DataProvider('isEmailHelperProvider')]
    public function testIsEmailHelper(string $email, bool $result): void
    {
        $this->assertSame(isEmail($email), $result);
    }
}
