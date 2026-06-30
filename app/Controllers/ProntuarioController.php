<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Paciente;
use App\Models\Prontuario;
use App\Models\ProntuarioAnexo;
use App\Models\ProntuarioEvolucao;
use App\Models\Medico;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Validator;
use App\Helpers\Redirect;
use App\Helpers\Logger;

class ProntuarioController extends Controller
{
    private const ACCESS_ROLES = ['administrador', 'funcionario', 'consultador', 'chefe_equipe', 'medico'];
    private const MAX_UPLOAD_SIZE = 5242880;

    private function loadProntuarioDocumentData(int $pacienteId): array
    {
        $pacienteModel = new Paciente();
        if (method_exists($pacienteModel, 'ensureCodesForAll')) {
            $pacienteModel->ensureCodesForAll();
        }

        $paciente = $pacienteModel->find($pacienteId);

        if (!$paciente) {
            Redirect::to('/pacientes')->with('error', 'Paciente não encontrado.');
        }

        $prontuarioModel = new Prontuario();
        $prontuario = $prontuarioModel->ensureForPaciente($pacienteId);

        $evolucaoModel = new ProntuarioEvolucao();
        $evolucoes = $evolucaoModel->findByProntuarioId($prontuario['id']);
        $anexoModel = new ProntuarioAnexo();
        $anexos = $anexoModel->findByProntuarioId($prontuario['id']);

        foreach ($anexos as &$anexo) {
            $anexoModel->migrateLegacyFile($anexo);
            $anexo['view_url'] = url("prontuarios/anexos/{$anexo['id']}");
        }
        unset($anexo);

        usort($anexos, function (array $left, array $right): int {
            return strcmp($left['registrado_em'], $right['registrado_em']);
        });

        return [
            'paciente' => $paciente,
            'prontuario' => $prontuario,
            'evolucoes' => $evolucoes,
            'anexos' => $anexos,
        ];
    }

    public function show($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['medico']);

        $this->view('prontuarios/show', $this->loadProntuarioDocumentData((int) $params['id']));
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
        $prontuario = $prontuarioModel->ensureForPaciente((int) $params['id']);

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

    public function storeAnexos($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(['medico']);
        (new \App\Middleware\CsrfMiddleware())->handle();

        $files = $_FILES['anexos'] ?? null;

        if (!$files || empty($files['name'][0])) {
            Redirect::to("/prontuarios/{$params['id']}")->with('error', 'Selecione pelo menos uma imagem para anexar.');
        }

        $pacienteModel = new Paciente();
        $paciente = $pacienteModel->find((int) $params['id']);

        if (!$paciente) {
            Redirect::to('/pacientes')->with('error', 'Paciente não encontrado.');
        }

        $prontuarioModel = new Prontuario();
        $prontuario = $prontuarioModel->ensureForPaciente((int) $params['id']);

        $medicoModel = new Medico();
        $medico = $medicoModel->findByUsuarioId(Session::get('usuario_id'));

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $anexoModel = new ProntuarioAnexo();
        $uploadDir = $anexoModel->ensureStorageDirectory();
        $uploadedCount = 0;

        foreach ($files['name'] as $index => $originalName) {
            if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                continue;
            }

            $tmpFile = $files['tmp_name'][$index];
            if (!is_uploaded_file($tmpFile)) {
                continue;
            }

            if (($files['size'][$index] ?? 0) > self::MAX_UPLOAD_SIZE) {
                continue;
            }

            $imageInfo = @getimagesize($tmpFile);
            $mimeType = $imageInfo['mime'] ?? '';

            if (!in_array($mimeType, $allowedMimeTypes, true)) {
                continue;
            }

            $extension = match ($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
                default => 'bin',
            };

            $storedName = bin2hex(random_bytes(16)) . '.' . $extension;
            $destination = $uploadDir . '/' . $storedName;

            if (!move_uploaded_file($tmpFile, $destination)) {
                continue;
            }

            @chmod($destination, 0640);

            $anexoModel->create([
                'prontuario_id' => $prontuario['id'],
                'medico_id' => $medico['id'],
                'nome_original' => basename($originalName),
                'caminho_arquivo' => $anexoModel->buildStoragePath($storedName),
                'mime_type' => $mimeType,
            ]);

            $uploadedCount++;
        }

        if ($uploadedCount === 0) {
            Redirect::to("/prontuarios/{$params['id']}")->with('error', 'Nenhuma imagem valida foi enviada.');
        }

        Logger::log('create', 'prontuario_anexos', $prontuario['id'], 'Imagens anexadas ao prontuário');

        Redirect::to("/prontuarios/{$params['id']}")->with('success', 'Imagem(ns) anexada(s) com sucesso!');
    }

    public function downloadPdf($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(self::ACCESS_ROLES);

        Redirect::to("/prontuarios/{$params['id']}/imprimir");
    }

    public function print($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(self::ACCESS_ROLES);

        $this->view('prontuarios/print', $this->loadProntuarioDocumentData((int) $params['id']), 'print');
    }

    public function showAnexo($params)
    {
        (new \App\Middleware\AuthMiddleware())->handle();
        (new \App\Middleware\RoleMiddleware())->handle(self::ACCESS_ROLES);

        $anexoModel = new ProntuarioAnexo();
        $anexo = $anexoModel->find((int) $params['id']);

        if (!$anexo) {
            http_response_code(404);
            exit('404 - Anexo não encontrado');
        }

        $absolutePath = $anexoModel->migrateLegacyFile($anexo);
        if (!$absolutePath || !is_file($absolutePath)) {
            http_response_code(404);
            exit('404 - Arquivo não encontrado');
        }

        $mimeType = mime_content_type($absolutePath) ?: ($anexo['mime_type'] ?? 'application/octet-stream');
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . (string) filesize($absolutePath));
        header('Content-Disposition: inline; filename="' . rawurlencode((string) ($anexo['nome_original'] ?? basename($absolutePath))) . '"');
        header('X-Content-Type-Options: nosniff');
        readfile($absolutePath);
        exit;
    }
}
