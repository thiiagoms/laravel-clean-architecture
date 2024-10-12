<?php

if (! function_exists('removeEmpty')) {

    function removeEmpty(array $payload): array
    {
        return array_filter($payload, fn (mixed $value): bool => ! empty($value));
    }
}
