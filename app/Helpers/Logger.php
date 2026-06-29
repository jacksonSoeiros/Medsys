<?php

namespace App\Helpers;

use App\Core\Database;

class Logger
{
    public static function log(string $acao, ?string $tabela_afetada = null, ?int $registro_id = null, ?string $descricao = null): void
    {
        $db = Database::getInstance()->getConnection();
        $usuario_id = Session::get('usuario_id');
        $ip_origem = $_SERVER['REMOTE_ADDR'] ?? null;

        $sql = "INSERT INTO logs (usuario_id, acao, tabela_afetada, registro_id, descricao, ip_origem) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$usuario_id, $acao, $tabela_afetada, $registro_id, $descricao, $ip_origem]);
    }
}

