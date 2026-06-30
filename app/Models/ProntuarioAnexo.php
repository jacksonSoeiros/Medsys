<?php

namespace App\Models;

use App\Core\Model;

class ProntuarioAnexo extends Model
{
    protected string $table = 'prontuario_anexos';
    private const PRIVATE_STORAGE_DIR = 'storage/private/prontuarios';
    private const LEGACY_PUBLIC_DIR = 'public/uploads/prontuarios';

    public function findByProntuarioId(int $prontuarioId): array
    {
        $sql = "SELECT a.*, m.nome_completo AS medico_nome
                FROM {$this->table} a
                JOIN medicos m ON m.id = a.medico_id
                WHERE a.prontuario_id = ?
                ORDER BY a.registrado_em DESC, a.id DESC";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$prontuarioId]);

        return $stmt->fetchAll();
    }

    public function ensureStorageDirectory(): string
    {
        $directory = dirname(__DIR__, 2) . '/' . self::PRIVATE_STORAGE_DIR;

        if (!is_dir($directory)) {
            mkdir($directory, 0750, true);
        }

        return $directory;
    }

    public function buildStoragePath(string $storedName): string
    {
        return self::PRIVATE_STORAGE_DIR . '/' . ltrim($storedName, '/');
    }

    public function resolveAbsolutePath(array $anexo): ?string
    {
        $normalizedPath = str_replace('\\', '/', (string) ($anexo['caminho_arquivo'] ?? ''));
        $projectRoot = dirname(__DIR__, 2);

        if ($normalizedPath !== '') {
            $candidate = $projectRoot . '/' . ltrim($normalizedPath, '/');
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        $legacyFileName = basename($normalizedPath);
        if ($legacyFileName === '') {
            return null;
        }

        $legacyPath = $projectRoot . '/' . self::LEGACY_PUBLIC_DIR . '/' . $legacyFileName;
        if (is_file($legacyPath)) {
            return $legacyPath;
        }

        return null;
    }

    public function migrateLegacyFile(array $anexo): ?string
    {
        $normalizedPath = str_replace('\\', '/', (string) ($anexo['caminho_arquivo'] ?? ''));
        if ($normalizedPath === '' || str_starts_with($normalizedPath, self::PRIVATE_STORAGE_DIR . '/')) {
            return $this->resolveAbsolutePath($anexo);
        }

        $sourcePath = $this->resolveAbsolutePath($anexo);
        if (!$sourcePath) {
            return null;
        }

        $this->ensureStorageDirectory();
        $targetRelativePath = $this->buildStoragePath(basename($sourcePath));
        $targetPath = dirname(__DIR__, 2) . '/' . $targetRelativePath;

        if (!is_file($targetPath)) {
            $moved = @rename($sourcePath, $targetPath);
            if (!$moved) {
                $moved = @copy($sourcePath, $targetPath);
                if ($moved) {
                    @unlink($sourcePath);
                }
            }
        } else {
            $moved = true;
        }

        if (!$moved || !is_file($targetPath)) {
            return $sourcePath;
        }

        $this->update((int) $anexo['id'], ['caminho_arquivo' => $targetRelativePath]);
        $anexo['caminho_arquivo'] = $targetRelativePath;

        return $targetPath;
    }
}
