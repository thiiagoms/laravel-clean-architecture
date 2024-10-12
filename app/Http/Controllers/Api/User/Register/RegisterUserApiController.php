<?php

namespace App\Http\Controllers\Api\User\Register;

use App\DTO\User\Register\RegisterUserDTO;
use App\Http\Controllers\Api\User\BaseUserApiController;
use App\Http\Requests\User\Register\RegisterUserRequest;
use App\Http\Resources\User\UserResource;

class RegisterUserApiController extends BaseUserApiController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterUserRequest $request): UserResource
    {
        $registerDTO = RegisterUserDTO::fromRequest($request);

        $user = $this->registerUserService->handle($registerDTO);

        return UserResource::make($user);
    }
}
