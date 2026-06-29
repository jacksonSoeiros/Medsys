<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use App\Models\Funcionario;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\Redirect;
use App\Helpers\Logger;

class FuncionarioController extends Controller
{
    public function index()
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador']);

        $funcionarioModel = new Funcionario();
        $funcionarios = $funcionarioModel->allWithUsuario();

        $this->view('funcionarios/index', ['funcionarios' => $funcionarios]);
    }

    public function create()
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador']);
        $this->view('funcionarios/create');
    }

    public function store()
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador']);
        (new \App\Middleware\CsrfMiddleware())->handle();

        $data = Security::sanitizeArray($_POST);

        $validator = new Validator($data);
        $validator->required('nome_completo')->required('email')->email('email')->required('senha')->minLength('senha', 6)->required('cpf')->cpf('cpf');

        if ($validator->fails()) {
            Redirect::to('/funcionarios/create')->withErrors($validator->getErrors())->withInput();
        }

        $usuarioModel = new Usuario();
        $existingUsuario = $usuarioModel->findBy('email', $data['email']);
        if ($existingUsuario) {
            Redirect::to('/funcionarios/create')->with('error', 'E-mail já cadastrado.')->withInput();
        }

        $usuarioId = $usuarioModel->create([
            'email' => $data['email'],
            'senha_hash' => Security::hashPassword($data['senha']),
            'papel' => 'funcionario',
            'ativo' => true
        ]);

        $funcionarioModel = new Funcionario();
        $funcionarioId = $funcionarioModel->create([
            'usuario_id' => $usuarioId,
            'nome_completo' => $data['nome_completo'],
            'cpf' => $data['cpf'],
            'telefone' => $data['telefone'] ?? null,
            'cargo' => $data['cargo'] ?? null
        ]);

        Logger::log('create', 'funcionarios', $funcionarioId, 'Funcionário criado');

        Redirect::to('/funcionarios')->with('success', 'Funcionário criado com sucesso!');
    }

    public function edit($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador']);

        $funcionarioModel = new Funcionario();
        $funcionario = $funcionarioModel->findByUsuarioId($params['id']);

        if (!$funcionario) {
            Redirect::to('/funcionarios')->with('error', 'Funcionário não encontrado.');
        }

        $this->view('funcionarios/edit', ['funcionario' => $funcionario]);
    }

    public function update($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador']);
        (new \App\Middleware\CsrfMiddleware())->handle();

        $data = Security::sanitizeArray($_POST);

        $funcionarioModel = new Funcionario();
        $funcionario = $funcionarioModel->find($params['id']);

        if (!$funcionario) {
            Redirect::to('/funcionarios')->with('error', 'Funcionário não encontrado.');
        }

        $validator = new Validator($data);
        $validator->required('nome_completo')->required('cpf')->cpf('cpf');

        if ($validator->fails()) {
            Redirect::to("/funcionarios/{$params['id']}/edit")->withErrors($validator->getErrors())->withInput();
        }

        $funcionarioModel->update($params['id'], [
            'nome_completo' => $data['nome_completo'],
            'cpf' => $data['cpf'],
            'telefone' => $data['telefone'] ?? null,
            'cargo' => $data['cargo'] ?? null
        ]);

        if (!empty($data['senha'])) {
            $usuarioModel = new Usuario();
            $usuarioModel->update($funcionario['usuario_id'], [
                'senha_hash' => Security::hashPassword($data['senha'])
            ]);
        }

        Logger::log('update', 'funcionarios', $params['id'], 'Funcionário atualizado');

        Redirect::to('/funcionarios')->with('success', 'Funcionário atualizado com sucesso!');
    }

    public function delete($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador']);

        $funcionarioModel = new Funcionario();
        $funcionario = $funcionarioModel->find($params['id']);

        if (!$funcionario) {
            Redirect::to('/funcionarios')->with('error', 'Funcionário não encontrado.');
        }

        $usuarioModel = new Usuario();
        $usuarioModel->delete($funcionario['usuario_id']);

        Logger::log('delete', 'funcionarios', $params['id'], 'Funcionário excluído');

        Redirect::to('/funcionarios')->with('success', 'Funcionário excluído com sucesso!');
    }
}

