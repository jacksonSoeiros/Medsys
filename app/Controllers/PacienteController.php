<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
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
    private function normalizePacienteData(array $data): array
    {
        $data['cpf'] = preg_replace('/\D+/', '', $data['cpf'] ?? '');
        $data['telefone'] = preg_replace('/\D+/', '', $data['telefone'] ?? '');
        $data['endereco_cep'] = preg_replace('/\D+/', '', $data['endereco_cep'] ?? '');

        return $data;
    }

    public function index()
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador', 'funcionario', 'consultador', 'chefe_equipe', 'medico']);

        $pacienteModel = new Paciente();
        $pacienteModel->ensureCodesForAll();
        $search = $_GET['search'] ?? '';
        $page = max(1, (int) ($_GET['page'] ?? 1));

        if (!empty($search)) {
            $pacientes = $pacienteModel->search($search);
            $totalPacientes = count($pacientes);
            $totalPages = 1;
        } else {
            $totalPacientes = $pacienteModel->countRecentModified();
            $totalPages = max(1, (int) ceil($totalPacientes / 10));
            $page = min($page, $totalPages);
            $pacientes = $pacienteModel->recentModifiedPaginated($page, 10);
        }

        $this->view('pacientes/index', [
            'pacientes' => $pacientes,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalPacientes' => $totalPacientes,
        ]);
    }

    public function create()
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador', 'funcionario', 'consultador', 'chefe_equipe']);
        $this->view('pacientes/create');
    }

    public function store()
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador', 'funcionario', 'consultador', 'chefe_equipe']);
        (new \App\Middleware\CsrfMiddleware())->handle();

        $data = $this->normalizePacienteData(Security::sanitizeArray($_POST));

        $validator = new Validator($data);
        $validator->required('nome_completo')->required('cpf')->cpf('cpf')->required('data_nascimento');

        if ($validator->fails()) {
            Redirect::to('/pacientes/create')->withErrors($validator->getErrors())->withInput();
        }

        // Get the current funcionario
        $funcionarioModel = new Funcionario();
        $funcionario = $funcionarioModel->findByUsuarioId(Session::get('usuario_id'));

        $pacienteModel = new Paciente();
        $pacienteModel->ensureCodesForAll();
        $connection = Database::getInstance()->getConnection();
        $prontuarioModel = new Prontuario();

        try {
            $connection->beginTransaction();

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

            $prontuarioModel->ensureForPaciente($pacienteId);

            $connection->commit();
        } catch (\Throwable $e) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            Redirect::to('/pacientes/create')->with('error', 'Nao foi possivel cadastrar o paciente e gerar o prontuario.')->withInput();
        }

        Logger::log('create', 'pacientes', $pacienteId, 'Paciente criado');

        Redirect::to('/pacientes')->with('success', 'Paciente criado com sucesso!');
    }

    public function show($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador', 'funcionario', 'consultador', 'chefe_equipe', 'medico']);

        $pacienteModel = new Paciente();
        $pacienteModel->ensureCodesForAll();
        $paciente = $pacienteModel->find($params['id']);

        if (!$paciente) {
            Redirect::to('/pacientes')->with('error', 'Paciente não encontrado.');
        }

        (new Prontuario())->ensureForPaciente((int) $params['id']);

        $this->view('pacientes/show', ['paciente' => $paciente]);
    }

    public function edit($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador', 'funcionario', 'consultador', 'chefe_equipe']);

        $pacienteModel = new Paciente();
        $pacienteModel->ensureCodesForAll();
        $paciente = $pacienteModel->find($params['id']);

        if (!$paciente) {
            Redirect::to('/pacientes')->with('error', 'Paciente não encontrado.');
        }

        $this->view('pacientes/edit', ['paciente' => $paciente]);
    }

    public function update($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['administrador', 'funcionario', 'consultador', 'chefe_equipe']);
        (new \App\Middleware\CsrfMiddleware())->handle();

        $data = $this->normalizePacienteData(Security::sanitizeArray($_POST));

        $pacienteModel = new Paciente();
        $pacienteModel->ensureCodesForAll();
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
        (new \App\Middleware\RoleMiddleware())->handle(['administrador', 'funcionario', 'consultador', 'chefe_equipe']);
        (new \App\Middleware\CsrfMiddleware())->handle();

        $pacienteModel = new Paciente();
        $pacienteModel->ensureCodesForAll();
        $paciente = $pacienteModel->find($params['id']);

        if (!$paciente) {
            Redirect::to('/pacientes')->with('error', 'Paciente não encontrado.');
        }

        $pacienteModel->delete($params['id']);

        Logger::log('delete', 'pacientes', $params['id'], 'Paciente excluído');

        Redirect::to('/pacientes')->with('success', 'Paciente excluído com sucesso!');
    }
}
