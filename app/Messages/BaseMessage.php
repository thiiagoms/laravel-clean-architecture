<?php

declare(strict_types=1);

namespace App\Messages;

abstract class BaseMessage
{
    /**
     * |----------------------------
     * | Field messages
     * |----------------------------
     */
    protected const FIELD_REQUIRED = "O campo '%s' é obrigatório.";

    protected const FIELD_MIN_LENGTH = "O campo '%s' deve ter um mínimo de %d caracteres";

    protected const FIELD_MAX_LENGTH = "O campo '%s' não deve exceder %d caracteres";

    protected const FIELD_TYPE = "O campo '%s' deve ser um(a) %s válido(a).";

    /**
     * |----------------------------
     * | Record messages
     * |----------------------------
     */
    protected const RECORD_ALREADY_EXISTS = '%s já existe.';
}
