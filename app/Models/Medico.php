<?php

namespace App\Models;

use App\Core\Model;

class Medico extends Model
{
    protected string $table = 'medicos';

    public function findByUsuarioId(int $usuario_id): ?array
    {
        $sql = "SELECT m.*, u.email, u.papel, u.ativo 
                FROM {$this->table} m 
                JOIN usuarios u ON m.usuario_id = u.id 
                WHERE m.usuario_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$usuario_id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function allWithUsuario(): array
    {
        $sql = "SELECT m.*, u.email, u.papel, u.ativo 
                FROM {$this->table} m 
                JOIN usuarios u ON m.usuario_id = u.id 
                ORDER BY m.id DESC";
        $stmt = $this->db->getConnection()->query($sql);
        return $stmt->fetchAll();
    }
}

