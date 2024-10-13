<?php

namespace Tests\Unit\Validtors\Auth;

use App\Validators\Auth\HashValidator;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HashValidatorTest extends TestCase
{
    public function testItShouldReturnTrueWhenPasswordMatchCorrectlyWithPasswordHash(): void
    {
        $password = 'password';

        /** @var HashValidator $hashValidator */
        $hashValidator = resolve(HashValidator::class);

        $result = $hashValidator->checkPasswordHashMatch($password, Hash::make($password));

        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    public function testItShouldReturnFalsewhenPasswordDoesNotMatchCorrectlyWithPasswordHash(): void
    {
        $password = 'password';

        /** @var HashValidator $hashValidator */
        $hashValidator = resolve(HashValidator::class);

        $result = $hashValidator->checkPasswordHashMatch($password, Hash::make('test'));

        $this->assertIsBool($result);
        $this->assertFalse($result);
    }
}
