<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
use App\Helpers\Redirect;

class HomeController extends Controller
{
    public function index(): void
    {
        if (Session::has('usuario_id')) {
            Redirect::to('/dashboard');
        } else {
            Redirect::to('/login');
        }
    }
}
