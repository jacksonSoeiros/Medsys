<?php

namespace App\Models;

use App\Core\Model;

class Funcionario extends Model
{
    protected string $table = 'funcionarios';

    public function findByUsuarioId(int $usuario_id): ?array
    {
        $sql = "SELECT f.*, u.email, u.papel, u.ativo 
                FROM {$this->table} f 
                JOIN usuarios u ON f.usuario_id = u.id 
                WHERE f.usuario_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$usuario_id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function allWithUsuario(): array
    {
        $sql = "SELECT f.*, u.email, u.papel, u.ativo 
                FROM {$this->table} f 
                JOIN usuarios u ON f.usuario_id = u.id 
                ORDER BY f.id DESC";
        $stmt = $this->db->getConnection()->query($sql);
        return $stmt->fetchAll();
    }
}

