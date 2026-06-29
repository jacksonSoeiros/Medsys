<?php

namespace App\Helpers;

class Security
{
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function generateCsrfToken(): string
    {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    public static function verifyCsrfToken(string $token): bool
    {
        return Session::has('csrf_token') && hash_equals(Session::get('csrf_token'), $token);
    }

    public static function sanitizeInput(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeArray(array $data): array
    {
        return array_map(function ($value) {
            return is_string($value) ? self::sanitizeInput($value) : $value;
        }, $data);
    }
}

