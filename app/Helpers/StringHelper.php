<?php

if (! function_exists('clean')) {
    function clean(string|array $payload): string|array
    {
        return gettype($payload) === 'array'
            ? array_map(fn (mixed $field): string => trim(strip_tags($field)), $payload)
            : trim(strip_tags($payload));
    }
}

if (! function_exists('isEmail')) {
    function isEmail(string $email): bool
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
