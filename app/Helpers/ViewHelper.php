<?php

namespace App\Helpers;

class ViewHelper
{
    public static function url(string $path = ''): string
    {
        $baseUrl = rtrim($_ENV['APP_URL'] ?? '/', '/');
        return $baseUrl . '/' . ltrim($path, '/');
    }

    public static function old(string $key, mixed $default = ''): mixed
    {
        $old = Session::flash('old');

        if (!is_array($old)) {
            return $default;
        }

        return $old[$key] ?? $default;
    }

    public static function asset(string $path): string
    {
        return self::url($path);
    }

    public static function csrf(): string
    {
        return Security::generateCsrfToken();
    }

    public static function e(?string $text): string
    {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }
}