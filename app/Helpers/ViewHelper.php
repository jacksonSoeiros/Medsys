<?php

namespace App\Helpers;

class ViewHelper
{
    public static function old(string $key, mixed $default = ''): mixed
    {
        return Session::flash('old')[$key] ?? $default;
    }
}

function old(string $key, mixed $default = ''): mixed
{
    return ViewHelper::old($key, $default);
}
