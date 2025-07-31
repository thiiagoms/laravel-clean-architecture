<?php

declare(strict_types=1);

namespace App\Application\UseCases\User\Register\DTO;

use App\Domain\Entity\User\ValueObject\Email;
use App\Domain\Entity\User\ValueObject\Name;
use App\Domain\Entity\User\ValueObject\Password;
use App\Infrastructure\Support\Sanitizer;
use App\Presentation\Api\V1\User\Requests\Register\RegisterUserApiRequest;

class RegisterUserDTO
{
    public function __construct(
        private readonly Name $name,
        private readonly Email $email,
        private readonly Password $password
    ) {}

    public function getName(): Name
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public static function fromRequest(RegisterUserApiRequest $request): RegisterUserDTO
    {
        $payload = Sanitizer::clean($request->validated());

        return new self(
            name: new Name($payload['name']),
            email: new Email($payload['email']),
            password: new Password($payload['password'])
        );
    }
}
