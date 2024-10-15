<?php

declare(strict_types=1);

namespace App\Virtual\Responses\Auth;

use OpenApi\Attributes as OA;

#[OA\Schema(
    description: 'User Token response for authenticated user',
    type: 'object',
    title: 'User authenticated response',
)]
class TokenVirtualResponse
{
    #[OA\Property(
        property: 'token',
        type: 'string',
        description: 'The token for the user.',
        example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c'
    )]
    public string $token;

    #[OA\Property(
        property: 'token_type',
        type: 'string',
        description: 'The type of the token.',
        example: 'Bearer'
    )]
    public string $token_type;

    #[OA\Property(
        property: 'expires_in',
        type: 'integer',
        description: 'The expiration time of the token.',
        example: 3600
    )]
    public int $expires_in;
}
