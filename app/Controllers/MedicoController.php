<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use App\Models\Medico;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\Redirect;
use App\Helpers\Logger;

class MedicoController extends Controller
{
    public function index()
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador']);

        $medicoModel = new Medico();
        $medicos = $medicoModel->allWithUsuario();

        $this->view('medicos/index', ['medicos' => $medicos]);
    }

    public function create()
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador']);
        $this->view('medicos/create');
    }

    public function store()
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador']);
        (new \App\Middleware\CsrfMiddleware())->handle();

        $data = Security::sanitizeArray($_POST);
        $data['cpf'] = preg_replace('/\D+/', '', $data['cpf'] ?? '');
        $data['telefone'] = preg_replace('/\D+/', '', $data['telefone'] ?? '');

        $validator = new Validator($data);
        $validator->required('nome_completo')->required('email')->email('email')->required('senha')->minLength('senha', 6)->required('cpf')->cpf('cpf')->required('crm');

        if ($validator->fails()) {
            Redirect::to('/medicos/create')->withErrors($validator->getErrors())->withInput();
        }

        $usuarioModel = new Usuario();
        $existingUsuario = $usuarioModel->findBy('email', $data['email']);
        if ($existingUsuario) {
            Redirect::to('/medicos/create')->with('error', 'E-mail já cadastrado.')->withInput();
        }

        $usuarioId = $usuarioModel->create([
            'email' => $data['email'],
            'senha_hash' => Security::hashPassword($data['senha']),
            'papel' => 'medico',
            'ativo' => true
        ]);

        $medicoModel = new Medico();
        $medicoId = $medicoModel->create([
            'usuario_id' => $usuarioId,
            'nome_completo' => $data['nome_completo'],
            'cpf' => $data['cpf'],
            'crm' => $data['crm'],
            'especialidade' => $data['especialidade'] ?? null,
            'telefone' => $data['telefone'] ?? null
        ]);

        Logger::log('create', 'medicos', $medicoId, 'Médico criado');

        Redirect::to('/medicos')->with('success', 'Médico criado com sucesso!');
    }

    public function edit($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador']);

        $medicoModel = new Medico();
        $medico = $medicoModel->findByUsuarioId($params['id']);

        if (!$medico) {
            Redirect::to('/medicos')->with('error', 'Médico não encontrado.');
        }

        $this->view('medicos/edit', ['medico' => $medico]);
    }

    public function update($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador']);
        (new \App\Middleware\CsrfMiddleware())->handle();

        $data = Security::sanitizeArray($_POST);
        $data['cpf'] = preg_replace('/\D+/', '', $data['cpf'] ?? '');
        $data['telefone'] = preg_replace('/\D+/', '', $data['telefone'] ?? '');

        $medicoModel = new Medico();
        $medico = $medicoModel->find($params['id']);

        if (!$medico) {
            Redirect::to('/medicos')->with('error', 'Médico não encontrado.');
        }

        $validator = new Validator($data);
        $validator->required('nome_completo')->required('cpf')->cpf('cpf')->required('crm');

        if ($validator->fails()) {
            Redirect::to("/medicos/{$params['id']}/edit")->withErrors($validator->getErrors())->withInput();
        }

        $medicoModel->update($params['id'], [
            'nome_completo' => $data['nome_completo'],
            'cpf' => $data['cpf'],
            'crm' => $data['crm'],
            'especialidade' => $data['especialidade'] ?? null,
            'telefone' => $data['telefone'] ?? null
        ]);

        if (!empty($data['senha'])) {
            $usuarioModel = new Usuario();
            $usuarioModel->update($medico['usuario_id'], [
                'senha_hash' => Security::hashPassword($data['senha'])
            ]);
        }

        Logger::log('update', 'medicos', $params['id'], 'Médico atualizado');

        Redirect::to('/medicos')->with('success', 'Médico atualizado com sucesso!');
    }

    public function delete($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador']);

        $medicoModel = new Medico();
        $medico = $medicoModel->find($params['id']);

        if (!$medico) {
            Redirect::to('/medicos')->with('error', 'Médico não encontrado.');
        }

        $usuarioModel = new Usuario();
        $usuarioModel->delete($medico['usuario_id']);

        Logger::log('delete', 'medicos', $params['id'], 'Médico excluído');

        Redirect::to('/medicos')->with('success', 'Médico excluído com sucesso!');
    }
}
