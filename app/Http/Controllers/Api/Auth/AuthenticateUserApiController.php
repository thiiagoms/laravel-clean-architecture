<?php

namespace App\Http\Controllers\Api\Auth;

use App\DTO\Auth\Authenticate\AuthenticateUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthenticateUserRequest;
use App\Http\Resources\Auth\TokenResource;
use App\Services\Auth\Authenticate\AuthenticateUserService;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;

class AuthenticateUserApiController extends Controller
{
    public function __construct(private readonly AuthenticateUserService $authenticateUserService) {}

    #[OA\Post(
        path: '/api/auth',
        tags: ['Auth'],
        summary: 'Authenticate user and return user token',
        description: 'Authenticate user by providing their email and password. If the credentials are valid, a token is returned which can be used to authenticate subsequent requests.',
        requestBody: new OA\RequestBody(
            description: 'User data for login',
            required: true,
            content: new JsonContent(
                ref: '#/components/schemas/AuthenticateUserRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        ref: '#/components/schemas/TokenVirtualResponse'
                    )
                )
            ),
            new OA\Response(
                response: 400,
                description: 'The server could not process the request due to invalid input.'
            ),
        ],
    )]
    public function __invoke(AuthenticateUserRequest $request): TokenResource
    {
        $authDTO = AuthenticateUserDTO::fromRequest($request);

        $token = $this->authenticateUserService->handle($authDTO);

        return TokenResource::make($token);
    }
}
