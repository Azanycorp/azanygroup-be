<?php


if (! function_exists('generate_user_verification_code')) {
    function generate_user_verification_code(): int
    {
        return mt_rand(1000, 9999);
    }
}