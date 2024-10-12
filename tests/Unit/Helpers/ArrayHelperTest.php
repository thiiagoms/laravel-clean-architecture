<?php

declare(strict_types=1);

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase
{
    public static function removeEmptyHelperProvider(): array
    {
        return [
            'should remove empty values from array and return new array without empty values' => [
                'payload' => [
                    'foo' => 'foo',
                    'bar' => 'bar',
                    'qux' => '',
                ],
                'result' => [
                    'foo' => 'foo',
                    'bar' => 'bar',
                ],
            ],
            'should return entire array if payload array is not empty' => [
                'payload' => [
                    'foo' => 'foo',
                    'bar' => 'bar',
                    'qux' => 'qux',
                ],
                'result' => [
                    'foo' => 'foo',
                    'bar' => 'bar',
                    'qux' => 'qux',
                ],
            ],
            'should return empty array if payload array is empty' => [
                'payload' => [],
                'result' => [],
            ],
        ];
    }

    #[DataProvider('removeEmptyHelperProvider')]
    public function testRemoveEmptyHelper(array $payload, array $result): void
    {
        $this->assertSame(removeEmpty($payload), $result);
    }
}
