<?php

namespace App\Middleware;

use App\Helpers\Session;
use App\Helpers\Redirect;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Session::has('usuario_id')) {
            Redirect::to('/login');
        }
    }
}

