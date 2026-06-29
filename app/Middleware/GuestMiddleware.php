<?php

namespace App\Middleware;

use App\Helpers\Session;
use App\Helpers\Redirect;

class GuestMiddleware
{
    public function handle(): void
    {
        if (Session::has('usuario_id')) {
            Redirect::to('/dashboard');
        }
    }
}

