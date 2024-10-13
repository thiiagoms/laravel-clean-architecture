<?php

namespace App\Http\Controllers\Api\Auth;

use App\DTO\Auth\Authenticate\AuthenticateUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthenticateUserRequest;
use App\Http\Resources\Auth\TokenResource;
use App\Services\Auth\Authenticate\AuthenticateUserService;

class AuthenticateUserApiController extends Controller
{
    public function __construct(private readonly AuthenticateUserService $authenticateUserService) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(AuthenticateUserRequest $request): TokenResource
    {
        $authDTO = AuthenticateUserDTO::fromRequest($request);

        $token = $this->authenticateUserService->handle($authDTO);

        return TokenResource::make($token);
    }
}
