<?php

namespace Tests\Unit\Domain\Common\ValueObject;

use App\Domain\Common\ValueObject\Id;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class IdTest extends TestCase
{
    public static function invalidIdCases(): array
    {
        return [
            'it should throw exception when provided id is not a valid UUID' => [
                'invalid-id',
            ],
            'it should throw exception when provided id is an empty string' => [
                '',
            ],
        ];
    }

    #[Test]
    #[DataProvider('invalidIdCases')]
    public function itShouldThrowExceptionWhenIdIsInvalid(string $id): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid id given: '{$id}'");

        new Id($id);
    }

    #[Test]
    public function itShouldCreateIdWhenIdIsValid(): void
    {
        $id = new Id('550e8400-e29b-41d4-a716-446655440000');

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $id->getValue());
    }
}
