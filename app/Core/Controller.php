<?php

namespace App\Core;

use App\Helpers\ViewHelper;

class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'app'): void
    {
        extract($data);

        ob_start();
        require dirname(__DIR__) . "/Views/{$view}.php";
        $content = ob_get_clean();

        require dirname(__DIR__) . "/Views/layouts/{$layout}.php";
    }
}
