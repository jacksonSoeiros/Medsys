<?php

namespace App\Models;

use App\Core\Model;

class Prontuario extends Model
{
    protected string $table = 'prontuarios';

    public function findByPacienteId(int $paciente_id): ?array
    {
        $sql = "SELECT p.*, pa.nome_completo as paciente_nome 
                FROM {$this->table} p 
                JOIN pacientes pa ON p.paciente_id = pa.id 
                WHERE p.paciente_id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$paciente_id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}

