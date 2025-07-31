<?php

namespace Tests\Unit\Infrastructure\Support;

use App\Infrastructure\Support\Sanitizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SanitizerTest extends TestCase
{
    public static function cleanCases(): array
    {
        return [
            'should remove spaces and html tags from string' => [
                ' <h1>Hello World</h1> ',
                'Hello World',
            ],
            'should remove spaces and html tags from each element of array' => [
                [
                    ' Hello World ',
                    ' <script>console.log("Hello World")</script> ',
                ],
                [
                    'Hello World',
                    'console.log("Hello World")',
                ],
            ],
            'should return empty array if input is empty' => [
                [],
                [],
            ],
        ];
    }

    #[Test]
    #[DataProvider('cleanCases')]
    public function itShouldCleanData(string|array $input, string|array $result): void
    {
        $this->assertEquals($result, Sanitizer::clean($input));
    }
}
