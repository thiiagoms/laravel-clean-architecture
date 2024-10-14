<?php

declare(strict_types=1);

namespace App\Messages\Auth;

abstract class AuthMessage
{
    public const string INVALID_CREDENTIALS = 'E-mail e/ou senha incorretos. Verifique e tente novamente mais tarde.';

    public const string UNAUTHENTICATED = 'Unauthenticated.';

    public const string UNAUTHORIZED = 'Não Autorizado.';
}
