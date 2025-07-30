<?php

namespace App\Http\Controllers\Api\User\Register;

use App\DTO\User\Register\RegisterUserDTO;
use App\Http\Controllers\Api\User\BaseUserApiController;
use App\Http\Requests\User\Register\RegisterUserRequest;
use App\Http\Resources\User\UserResource;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Response;

class RegisterUserApiController extends BaseUserApiController
{
    #[OA\Post(
        path: '/api/register',
        tags: ['User'],
        summary: 'Register new user',
        description: "Register a new user and receive the user's data upon successful creation.",
        requestBody: new OA\RequestBody(
            description: 'User data for registration',
            required: true,
            content: new JsonContent(
                ref: '#/components/schemas/RegisterUserVirtualRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Success response',
                content: new JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        ref: '#/components/schemas/UserVirtualResponse'
                    )
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'The server could not process the request due to invalid input.'
            ),
        ]
    )]
    public function __invoke(RegisterUserRequest $request): UserResource
    {
        $registerDTO = RegisterUserDTO::fromRequest($request);

        $user = $this->registerUserService->handle($registerDTO);

        return UserResource::make($user);
    }
}
