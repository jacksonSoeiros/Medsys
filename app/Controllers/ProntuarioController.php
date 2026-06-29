<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Paciente;
use App\Models\Prontuario;
use App\Models\ProntuarioEvolucao;
use App\Models\Medico;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\Redirect;
use App\Helpers\Logger;

class ProntuarioController extends Controller
{
    public function show($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['medico']);

        $pacienteModel = new Paciente();
        $paciente = $pacienteModel->find($params['id']);

        if (!$paciente) {
            Redirect::to('/pacientes')->with('error', 'Paciente não encontrado.');
        }

        $prontuarioModel = new Prontuario();
        $prontuario = $prontuarioModel->findByPacienteId($params['id']);

        $evolucaoModel = new ProntuarioEvolucao();
        $evolucoes = $evolucaoModel->findByProntuarioId($prontuario['id']);

        $this->view('prontuarios/show', [
            'paciente' => $paciente,
            'prontuario' => $prontuario,
            'evolucoes' => $evolucoes
        ]);
    }

    public function storeEvolucao($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['medico']);
        (new \App\Middleware\CsrfMiddleware())->handle();

        $data = Security::sanitizeArray($_POST);

        $validator = new Validator($data);
        $validator->required('texto_evolucao');

        if ($validator->fails()) {
            Redirect::to("/prontuarios/{$params['id']}")->withErrors($validator->getErrors());
        }

        $prontuarioModel = new Prontuario();
        $prontuario = $prontuarioModel->findByPacienteId($params['id']);

        if (!$prontuario) {
            Redirect::to('/pacientes')->with('error', 'Prontuário não encontrado.');
        }

        // Get current medico
        $medicoModel = new Medico();
        $medico = $medicoModel->findByUsuarioId(Session::get('usuario_id'));

        $evolucaoModel = new ProntuarioEvolucao();
        $evolucaoId = $evolucaoModel->create([
            'prontuario_id' => $prontuario['id'],
            'medico_id' => $medico['id'],
            'texto_evolucao' => $data['texto_evolucao']
        ]);

        Logger::log('create', 'prontuario_evolucoes', $evolucaoId, 'Evolução adicionada ao prontuário');

        Redirect::to("/prontuarios/{$params['id']}")->with('success', 'Evolução adicionada com sucesso!');
    }
}

