<?php

declare(strict_types=1);

namespace App\Application\UseCases\Auth\Authenticate\DTO;

use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Password;
use App\Infrastructure\Support\Sanitizer;
use App\Presentation\Api\V1\Auth\Requests\Authenticate\AuthenticateUserApiRequest;

class AuthenticateDTO
{
    public function __construct(private Email $email, private Password $password) {}

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public static function fromRequest(AuthenticateUserApiRequest $request): self
    {
        $payload = Sanitizer::clean($request->validated());

        return new self(
            email: new Email($payload['email']),
            password: new Password(password: $payload['password'], hashed: false)
        );
    }
}
