<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Paciente;
use App\Models\Funcionario;
use App\Models\Prontuario;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\Redirect;
use App\Helpers\Logger;

class PacienteController extends Controller
{
    public function index()
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador', 'funcionario', 'medico']);

        $pacienteModel = new Paciente();
        $search = $_GET['search'] ?? '';
        
        if (!empty($search)) {
            $pacientes = $pacienteModel->search($search);
        } else {
            $pacientes = $pacienteModel->all();
        }

        $this->view('pacientes/index', ['pacientes' => $pacientes, 'search' => $search]);
    }

    public function create()
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador', 'funcionario']);
        $this->view('pacientes/create');
    }

    public function store()
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador', 'funcionario']);
        (new \App\Middleware\CsrfMiddleware())->handle();

        $data = Security::sanitizeArray($_POST);

        $validator = new Validator($data);
        $validator->required('nome_completo')->required('cpf')->cpf('cpf')->required('data_nascimento');

        if ($validator->fails()) {
            Redirect::to('/pacientes/create')->withErrors($validator->getErrors())->withInput();
        }

        // Get the current funcionario
        $funcionarioModel = new Funcionario();
        $funcionario = $funcionarioModel->findByUsuarioId(Session::get('usuario_id'));

        $pacienteModel = new Paciente();
        $pacienteId = $pacienteModel->create([
            'nome_completo' => $data['nome_completo'],
            'cpf' => $data['cpf'],
            'data_nascimento' => $data['data_nascimento'],
            'telefone' => $data['telefone'] ?? null,
            'endereco_logradouro' => $data['endereco_logradouro'] ?? null,
            'endereco_numero' => $data['endereco_numero'] ?? null,
            'endereco_complemento' => $data['endereco_complemento'] ?? null,
            'endereco_bairro' => $data['endereco_bairro'] ?? null,
            'endereco_cidade' => $data['endereco_cidade'] ?? null,
            'endereco_uf' => $data['endereco_uf'] ?? null,
            'endereco_cep' => $data['endereco_cep'] ?? null,
            'cadastrado_por' => $funcionario['id'] ?? null
        ]);

        // Create prontuario
        $prontuarioModel = new Prontuario();
        $prontuarioModel->create([
            'paciente_id' => $pacienteId
        ]);

        Logger::log('create', 'pacientes', $pacienteId, 'Paciente criado');

        Redirect::to('/pacientes')->with('success', 'Paciente criado com sucesso!');
    }

    public function show($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador', 'funcionario', 'medico']);

        $pacienteModel = new Paciente();
        $paciente = $pacienteModel->find($params['id']);

        if (!$paciente) {
            Redirect::to('/pacientes')->with('error', 'Paciente não encontrado.');
        }

        $this->view('pacientes/show', ['paciente' => $paciente]);
    }

    public function edit($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador', 'funcionario']);

        $pacienteModel = new Paciente();
        $paciente = $pacienteModel->find($params['id']);

        if (!$paciente) {
            Redirect::to('/pacientes')->with('error', 'Paciente não encontrado.');
        }

        $this->view('pacientes/edit', ['paciente' => $paciente]);
    }

    public function update($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador', 'funcionario']);
        (new \App\Middleware\CsrfMiddleware())->handle();

        $data = Security::sanitizeArray($_POST);

        $pacienteModel = new Paciente();
        $paciente = $pacienteModel->find($params['id']);

        if (!$paciente) {
            Redirect::to('/pacientes')->with('error', 'Paciente não encontrado.');
        }

        $validator = new Validator($data);
        $validator->required('nome_completo')->required('cpf')->cpf('cpf')->required('data_nascimento');

        if ($validator->fails()) {
            Redirect::to("/pacientes/{$params['id']}/edit")->withErrors($validator->getErrors())->withInput();
        }

        $pacienteModel->update($params['id'], [
            'nome_completo' => $data['nome_completo'],
            'cpf' => $data['cpf'],
            'data_nascimento' => $data['data_nascimento'],
            'telefone' => $data['telefone'] ?? null,
            'endereco_logradouro' => $data['endereco_logradouro'] ?? null,
            'endereco_numero' => $data['endereco_numero'] ?? null,
            'endereco_complemento' => $data['endereco_complemento'] ?? null,
            'endereco_bairro' => $data['endereco_bairro'] ?? null,
            'endereco_cidade' => $data['endereco_cidade'] ?? null,
            'endereco_uf' => $data['endereco_uf'] ?? null,
            'endereco_cep' => $data['endereco_cep'] ?? null
        ]);

        Logger::log('update', 'pacientes', $params['id'], 'Paciente atualizado');

        Redirect::to('/pacientes')->with('success', 'Paciente atualizado com sucesso!');
    }

    public function delete($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador', 'funcionario']);

        $pacienteModel = new Paciente();
        $paciente = $pacienteModel->find($params['id']);

        if (!$paciente) {
            Redirect::to('/pacientes')->with('error', 'Paciente não encontrado.');
        }

        $pacienteModel->delete($params['id']);

        Logger::log('delete', 'pacientes', $params['id'], 'Paciente excluído');

        Redirect::to('/pacientes')->with('success', 'Paciente excluído com sucesso!');
    }
}

