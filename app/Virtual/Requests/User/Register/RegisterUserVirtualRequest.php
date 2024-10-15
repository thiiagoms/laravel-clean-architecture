<?php

declare(strict_types=1);

namespace App\Virtual\Requests\User\Register;

use OpenApi\Attributes as OA;

#[OA\Schema(
    description: 'Request to create new user on system',
    type: 'object',
    title: 'User register request',
)]
class RegisterUserVirtualRequest
{
    #[OA\Property(
        property: 'name',
        type: 'string',
        description: 'The name of the user.',
        example: 'John Doe'
    )]
    public string $name;

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
