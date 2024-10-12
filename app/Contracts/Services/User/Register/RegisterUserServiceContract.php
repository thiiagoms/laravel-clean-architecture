<?php

declare(strict_types=1);

namespace App\Contracts\Services\User\Register;

use App\DTO\User\Register\RegisterUserDTO;
use App\Models\User;

interface RegisterUserServiceContract
{
    public function handle(RegisterUserDTO $registerUserDTO): User;
}
