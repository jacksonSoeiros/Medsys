<?php

namespace App\Models;

use App\Core\Model;

class Paciente extends Model
{
    protected string $table = 'pacientes';

    public function create(array $data): int
    {
        $patientId = parent::create($data);
        $this->ensureCode($patientId);

        return $patientId;
    }

    public function ensureCode(int $patientId): void
    {
        $sql = "UPDATE {$this->table}
                SET codigo_paciente = COALESCE(codigo_paciente, ?)
                WHERE id = ?";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$patientId, $patientId]);
    }

    public function ensureCodesForAll(): void
    {
        $sql = "UPDATE {$this->table}
                SET codigo_paciente = id
                WHERE codigo_paciente IS NULL";
        $this->db->getConnection()->exec($sql);
    }

    public function search(string $term): array
    {
        $digits = preg_replace('/\D+/', '', $term);
        $sql = "SELECT * FROM {$this->table}
                WHERE nome_completo ILIKE ?
                OR cpf ILIKE ?
                OR endereco_cidade ILIKE ?
                OR CAST(codigo_paciente AS TEXT) ILIKE ?
                ORDER BY nome_completo ASC";
        $term = "%{$term}%";
        $codeTerm = '%' . $digits . '%';
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$term, $term, $term, $codeTerm]);
        return $stmt->fetchAll();
    }

    public function countRecentModified(int $days = 30): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE atualizado_em >= (NOW() - INTERVAL '{$days} days')";
        $stmt = $this->db->getConnection()->query($sql);

        return (int) $stmt->fetchColumn();
    }

    public function recentModifiedPaginated(int $page = 1, int $perPage = 10, int $days = 30): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $sql = "SELECT * FROM {$this->table}
                WHERE atualizado_em >= (NOW() - INTERVAL '{$days} days')
                ORDER BY atualizado_em DESC, id DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
