<?php

namespace App\Middleware;

use App\Helpers\Session;
use App\Helpers\Redirect;

class RoleMiddleware
{
    public function handle(array $roles): void
    {
        if (!Session::has('usuario_papel') || !in_array(Session::get('usuario_papel'), $roles)) {
            http_response_code(403);
            echo "403 - Acesso negado";
            exit;
        }
    }
}

