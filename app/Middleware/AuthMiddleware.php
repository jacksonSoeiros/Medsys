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

        if (Session::isExpired()) {
            Session::destroy();
            Redirect::to('/login')->with('error', 'Sua sessão expirou por inatividade.');
        }

        Session::touchActivity();
    }
}
