<?php

namespace App\Helpers;

class ViewHelper
{
    public static function old(string $key, mixed $default = ''): mixed
    {
        return Session::flash('old')[$key] ?? $default;
    }

    public static function url(string $path = ''): string
    {
        $baseUrl = rtrim($_ENV['APP_URL'] ?? '/', '/');
        return $baseUrl . '/' . ltrim($path, '/');
    }
}

function old(string $key, mixed $default = ''): mixed
{
    return ViewHelper::old($key, $default);
}

function url(string $path = ''): string
{
    return ViewHelper::url($path);
}
