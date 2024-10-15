<?php

declare(strict_types=1);

namespace App\Virtual\Requests\Auth;

use OpenApi\Attributes as OA;

#[OA\Schema(
    description: 'Basic request for user authentication',
    type: 'object',
    title: 'User authentication request',
)]
class AuthenticateUserRequest
{
    #[OA\Property(
        property: 'email',
        type: 'string',
        description: 'The email address of the user.',
        format: 'email',
        example: 'john@example.com'
    )]
    public string $email;

    #[OA\Property(
        property: 'password',
        type: 'string',
        description: 'The password for the user.',
        example: '@p5sSw0rd!'
    )]
    public string $password;
}
