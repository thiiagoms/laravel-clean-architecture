<?php

if (! function_exists('isEmail')) {
    function isEmail(string $email): bool
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
