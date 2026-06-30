<?php

namespace App\Helpers {
    class ViewHelper
    {
        public static function url(string $path = ''): string
        {
            $baseUrl = rtrim($_ENV['APP_URL'] ?? '/', '/');
            return $baseUrl . '/' . ltrim($path, '/');
        }

        public static function old(string $key, mixed $default = ''): mixed
        {
            $old = Session::flash('old');

            if (!is_array($old)) {
                return $default;
            }

            return $old[$key] ?? $default;
        }

        public static function asset(string $path): string
        {
            return self::url($path);
        }

        public static function csrf(): string
        {
            return Security::generateCsrfToken();
        }

        public static function e(?string $text): string
        {
            return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
        }

        public static function formatCpf(?string $cpf): string
        {
            $digits = preg_replace('/\D+/', '', $cpf ?? '');

            if (strlen($digits) !== 11) {
                return $cpf ?? '';
            }

            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digits);
        }

        public static function formatPhone(?string $phone): string
        {
            $digits = preg_replace('/\D+/', '', $phone ?? '');

            if (strlen($digits) === 11) {
                return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $digits);
            }

            if (strlen($digits) === 10) {
                return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $digits);
            }

            return $phone ?? '';
        }

        public static function formatCep(?string $cep): string
        {
            $digits = preg_replace('/\D+/', '', $cep ?? '');

            if (strlen($digits) !== 8) {
                return $cep ?? '';
            }

            return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $digits);
        }

        public static function roleLabel(?string $role): string
        {
            return match ($role) {
                'administrador' => 'Admin',
                'consultador' => 'Consultador',
                'chefe_equipe' => 'Chefe de Equipe',
                'funcionario' => 'Funcionario',
                'medico' => 'Medico',
                default => (string) ($role ?? ''),
            };
        }

        public static function formatPatientCode(null|int|string $code): string
        {
            if ($code === null || $code === '') {
                return '';
            }

            if (is_numeric($code)) {
                return 'PAC-' . str_pad((string) $code, 6, '0', STR_PAD_LEFT);
            }

            return (string) $code;
        }
    }
}

namespace {
    function url(string $path = ''): string
    {
        return \App\Helpers\ViewHelper::url($path);
    }

    function old(string $key, mixed $default = ''): mixed
    {
        return \App\Helpers\ViewHelper::old($key, $default);
    }

    function asset(string $path): string
    {
        return \App\Helpers\ViewHelper::asset($path);
    }

    function csrf(): string
    {
        return \App\Helpers\ViewHelper::csrf();
    }

    function e(?string $text): string
    {
        return \App\Helpers\ViewHelper::e($text);
    }

    function formatCpf(?string $cpf): string
    {
        return \App\Helpers\ViewHelper::formatCpf($cpf);
    }

    function formatPhone(?string $phone): string
    {
        return \App\Helpers\ViewHelper::formatPhone($phone);
    }

    function formatCep(?string $cep): string
    {
        return \App\Helpers\ViewHelper::formatCep($cep);
    }

    function roleLabel(?string $role): string
    {
        return \App\Helpers\ViewHelper::roleLabel($role);
    }

    function formatPatientCode(null|int|string $code): string
    {
        return \App\Helpers\ViewHelper::formatPatientCode($code);
    }
}
