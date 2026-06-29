<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use App\Models\Funcionario;
use App\Models\Medico;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\Redirect;
use App\Helpers\Logger;

class AuthController extends Controller
{
    public function login()
    {
        (new \App\Middleware\GuestMiddleware())->handle();
        $this->view('auth/login', [], 'base');
    }

    public function doLogin()
    {
        (new \App\Middleware\GuestMiddleware())->handle();
        (new \App\Middleware\CsrfMiddleware())->handle();

        $data = Security::sanitizeArray($_POST);

        $validator = new Validator($data);
        $validator->required('email')->email('email')->required('senha');

        if ($validator->fails()) {
            Redirect::to('/login')->withErrors($validator->getErrors())->withInput();
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->findBy('email', $data['email']);

        if (!$usuario || !$usuario['ativo']) {
            Redirect::to('/login')->with('error', 'E-mail ou senha inválidos.')->withInput();
        }

        if (!Security::verifyPassword($data['senha'], $usuario['senha_hash'])) {
            Redirect::to('/login')->with('error', 'E-mail ou senha inválidos.')->withInput();
        }

        Session::set('usuario_id', $usuario['id']);
        Session::set('usuario_papel', $usuario['papel']);
        Session::set('usuario_email', $usuario['email']);

        if ($usuario['papel'] === 'medico') {
            $medicoModel = new Medico();
            $medico = $medicoModel->findByUsuarioId($usuario['id']);
            Session::set('usuario_nome', $medico['nome_completo']);
        } else {
            $funcionarioModel = new Funcionario();
            $funcionario = $funcionarioModel->findByUsuarioId($usuario['id']);
            Session::set('usuario_nome', $funcionario['nome_completo']);
        }

        Logger::log('login', 'usuarios', $usuario['id'], 'Usuário logou no sistema');

        Redirect::to('/dashboard');
    }

    public function logout()
    {
        Logger::log('logout', 'usuarios', Session::get('usuario_id'), 'Usuário deslogou do sistema');
        Session::destroy();
        Redirect::to('/login');
    }

    public function dashboard()
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        $this->view('dashboard', [
            'usuario_nome' => Session::get('usuario_nome'),
            'usuario_papel' => Session::get('usuario_papel')
        ]);
    }
}

