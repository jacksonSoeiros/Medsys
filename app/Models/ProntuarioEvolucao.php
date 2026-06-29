<?php

namespace App\Models;

use App\Core\Model;

class ProntuarioEvolucao extends Model
{
    protected string $table = 'prontuario_evolucoes';

    public function findByProntuarioId(int $prontuario_id): array
    {
        $sql = "SELECT e.*, m.nome_completo as medico_nome 
                FROM {$this->table} e 
                JOIN medicos m ON e.medico_id = m.id 
                WHERE e.prontuario_id = ? 
                ORDER BY e.registrado_em DESC";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$prontuario_id]);
        return $stmt->fetchAll();
    }
}

