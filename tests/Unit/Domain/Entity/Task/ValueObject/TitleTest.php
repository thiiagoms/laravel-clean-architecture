<?php

namespace Tests\Unit\Domain\Entity\Task\ValueObject;

use App\Domain\Entity\Task\ValueObject\Title;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TitleTest extends TestCase
{
    #[Test]
    public function itShouldThrowExceptionWhenTitleIsNotAString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Title must be a valid string and cannot be longer than 100 characters.');

        new Title('');
    }

    #[Test]
    public function itShouldThrowExceptionWhenTitleIsTooLong(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Title must be a valid string and cannot be longer than 100 characters.');

        new Title(str_repeat('a', 101));
    }

    #[Test]
    public function itShouldCreateTitleSuccessfully(): void
    {
        $title = new Title('Valid Title');

        $this->assertSame('Valid Title', $title->getValue());
    }
}
