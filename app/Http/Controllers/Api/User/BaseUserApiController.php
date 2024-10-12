<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Services\User\Register\RegisterUserService;

abstract class BaseUserApiController extends Controller
{
    public function __construct(protected readonly RegisterUserService $registerUserService) {}
}
