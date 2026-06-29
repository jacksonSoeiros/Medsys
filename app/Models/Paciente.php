<?php

namespace App\Models;

use App\Core\Model;

class Paciente extends Model
{
    protected string $table = 'pacientes';

    public function search(string $term): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE nome_completo ILIKE ? 
                OR cpf ILIKE ? 
                OR endereco_cidade ILIKE ?
                ORDER BY nome_completo ASC";
        $term = "%{$term}%";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$term, $term, $term]);
        return $stmt->fetchAll();
    }
}

