<?php

namespace Tests\Unit\Validators\Uuid;

use App\Exceptions\LogicalException;
use App\Messages\System\SystemMessage;
use App\Validators\Uuid\UuidValidator;
use Tests\TestCase;

class UuidValidatorTest extends TestCase
{
    public function testItShouldThrowLogicalExceptionWithInvalidParameterMessageWhenIdProvidedIsNotAValidUuid(): void
    {

        /** @var UuidValidator $uuidValidator */
        $uuidValidator = resolve(UuidValidator::class);

        $this->expectException(LogicalException::class);
        $this->expectExceptionMessage(SystemMessage::INVALID_PARAMETER);

        $uuidValidator->checkUuidIsValid('invalid-uuid');
    }

    public function testItShouldReturnNullAndNotThrowExceptionWhenIdProvidedIsValidUuid(): void
    {
        /** @var UuidValidator $uuidValidator */
        $uuidValidator = resolve(UuidValidator::class);

        $this->expectNotToPerformAssertions();

        $uuidValidator->checkUuidIsValid(fake()->uuid());
    }
}
