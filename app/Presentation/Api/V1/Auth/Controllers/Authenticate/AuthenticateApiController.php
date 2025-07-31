<?php

namespace App\Presentation\Api\V1\Auth\Controllers\Authenticate;

use App\Application\UseCases\Auth\Authenticate\Authenticate;
use App\Application\UseCases\Auth\Authenticate\DTO\AuthenticateDTO;
use App\Http\Controllers\Controller;
use App\Presentation\Api\V1\Auth\Requests\Authenticate\AuthenticateUserApiRequest;
use App\Presentation\Api\V1\Auth\Resources\TokenResource;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiController extends Controller
{
    public function __construct(private readonly Authenticate $action) {}

    #[Post(
        path: '/api/v1/auth/login',
        description: 'Authenticate user by providing their email and password. If the credentials are valid, a token is returned which can be used to authenticate subsequent requests.',
        summary: 'Authenticate user and return token',
        requestBody: new RequestBody(
            description: 'User data for login',
            required: true,
            content: new JsonContent(
                ref: '#/components/schemas/AuthenticateUserSwaggerRequest'
            )
        ),
        tags: ['Auth'],
        responses: [
            new \OpenApi\Attributes\Response(
                response: Response::HTTP_OK,
                description: 'Successful operation',
                content: new JsonContent(
                    type: 'array',
                    items: new Items(
                        ref: '#/components/schemas/TokenSwaggerResponse',
                        type: 'object'
                    )
                )
            ),
            new \OpenApi\Attributes\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'The server could not process the request due to invalid input.'
            ),
        ],
    )]
    public function __invoke(AuthenticateUserApiRequest $request): TokenResource
    {
        $dto = AuthenticateDTO::fromRequest($request);

        $token = $this->action->handle($dto);

        return TokenResource::make($token);
    }
}
