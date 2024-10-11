<?php

declare(strict_types=1);

namespace App\Messages\System;

abstract class SystemMessage
{
    public const string RESOURCE_NOT_FOUND = 'Recurso não encontrado.';

    public const string INVALID_PARAMETER = 'Parâmetro inválido fornecido';

    public const string GENERIC_ERROR = 'Algo deu errado, tente novamente mais tarde.';
}
