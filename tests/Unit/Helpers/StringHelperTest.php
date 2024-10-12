<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    public static function cleanHelperProvider(): array
    {
        return [
            'should remove spaces and html tags from string' => [
                'payload' => ' <h1>Hello World</h1> ',
                'result' => 'Hello World',
            ],
            'should remove spaces and html tags from each element of array' => [
                'payload' => [
                    ' Hello World ',
                    ' <script>console.log("Hello World")</script> ',
                ],
                'result' => [
                    'Hello World',
                    'console.log("Hello World")',
                ],
            ],
            'should return empty array if payload is empty' => [
                'payload' => [],
                'result' => [],
            ],
        ];
    }

    #[DataProvider('cleanHelperProvider')]
    public function testCleanHelper(string|array $payload, string|array $result): void
    {
        $this->assertSame(clean($payload), $result);
    }

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
