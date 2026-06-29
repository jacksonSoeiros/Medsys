<?php

namespace App\Helpers;

class Validator
{
    private array $errors = [];
    private array $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $message = null): self
    {
        if (!isset($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->errors[$field] = $message ?? "O campo {$field} é obrigatório.";
        }
        return $this;
    }

    public function email(string $field, string $message = null): self
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? "O campo {$field} deve ser um e-mail válido.";
        }
        return $this;
    }

    public function minLength(string $field, int $length, string $message = null): self
    {
        if (isset($this->data[$field]) && strlen(trim($this->data[$field])) < $length) {
            $this->errors[$field] = $message ?? "O campo {$field} deve ter pelo menos {$length} caracteres.";
        }
        return $this;
    }

    public function maxLength(string $field, int $length, string $message = null): self
    {
        if (isset($this->data[$field]) && strlen(trim($this->data[$field])) > $length) {
            $this->errors[$field] = $message ?? "O campo {$field} deve ter no máximo {$length} caracteres.";
        }
        return $this;
    }

    public function cpf(string $field, string $message = null): self
    {
        if (isset($this->data[$field])) {
            $cpf = preg_replace('/[^0-9]/', '', $this->data[$field]);
            if (strlen($cpf) !== 11 || !$this->validateCpf($cpf)) {
                $this->errors[$field] = $message ?? "O CPF informado é inválido.";
            }
        }
        return $this;
    }

    private function validateCpf(string $cpf): bool
    {
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

