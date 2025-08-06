<?php

namespace Tests\Unit\Domain\Entity\Task\ValueObject;

use App\Domain\Entity\Task\ValueObject\Title;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TitleTest extends TestCase
{
    #[Test]
    public function it_should_throw_exception_when_title_is_not_a_string(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Title must be a valid string and cannot be longer than 100 characters.');

        new Title('');
    }

    #[Test]
    public function it_should_throw_exception_when_title_is_too_long(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Title must be a valid string and cannot be longer than 100 characters.');

        new Title(str_repeat('a', 101));
    }

    #[Test]
    public function it_should_create_title_successfully(): void
    {
        $title = new Title('Valid Title');

        $this->assertSame('Valid Title', $title->getValue());
    }
}
