<?php

namespace App\Core;

class Request
{
    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function uri(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    public static function all(): array
    {
        return $_REQUEST;
    }
}

?>