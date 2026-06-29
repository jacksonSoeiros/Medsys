<?php

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\FuncionarioController;
use App\Controllers\MedicoController;
use App\Controllers\PacienteController;
use App\Controllers\ProntuarioController;

/*
|--------------------------------------------------------------------------
| Rotas Web
|--------------------------------------------------------------------------
*/

$router->get('/', [HomeController::class, 'index']);

// Auth
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'doLogin']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/dashboard', [AuthController::class, 'dashboard']);

// Funcionários
$router->get('/funcionarios', [FuncionarioController::class, 'index']);
$router->get('/funcionarios/create', [FuncionarioController::class, 'create']);
$router->post('/funcionarios', [FuncionarioController::class, 'store']);
$router->get('/funcionarios/{id}/edit', [FuncionarioController::class, 'edit']);
$router->post('/funcionarios/{id}', [FuncionarioController::class, 'update']);
$router->get('/funcionarios/{id}/delete', [FuncionarioController::class, 'delete']);

// Médicos
$router->get('/medicos', [MedicoController::class, 'index']);
$router->get('/medicos/create', [MedicoController::class, 'create']);
$router->post('/medicos', [MedicoController::class, 'store']);
$router->get('/medicos/{id}/edit', [MedicoController::class, 'edit']);
$router->post('/medicos/{id}', [MedicoController::class, 'update']);
$router->get('/medicos/{id}/delete', [MedicoController::class, 'delete']);

// Pacientes
$router->get('/pacientes', [PacienteController::class, 'index']);
$router->get('/pacientes/create', [PacienteController::class, 'create']);
$router->post('/pacientes', [PacienteController::class, 'store']);
$router->get('/pacientes/{id}', [PacienteController::class, 'show']);
$router->get('/pacientes/{id}/edit', [PacienteController::class, 'edit']);
$router->post('/pacientes/{id}', [PacienteController::class, 'update']);
$router->get('/pacientes/{id}/delete', [PacienteController::class, 'delete']);

// Prontuários
$router->get('/prontuarios/{id}', [ProntuarioController::class, 'show']);
$router->post('/prontuarios/{id}/evolucoes', [ProntuarioController::class, 'storeEvolucao']);

?>