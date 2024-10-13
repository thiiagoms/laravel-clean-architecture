<?php

declare(strict_types=1);

namespace App\Contracts\Services\Auth\Token;

use Illuminate\Auth\AuthenticationException;

interface TokenExceptionHandlerContract
{
    /**
     * @throws AuthenticationException
     */
    public function handle(string|bool $token): string;
}
