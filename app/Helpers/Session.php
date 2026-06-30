<?php

namespace App\Helpers;

class Session
{
    private static function timeoutInSeconds(): int
    {
        $timeout = (int) ($_ENV['SESSION_TIMEOUT'] ?? 600);
        return $timeout > 0 ? $timeout : 600;
    }

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function regenerate(bool $deleteOldSession = true): void
    {
        self::start();
        session_regenerate_id($deleteOldSession);
    }

    public static function touchActivity(): void
    {
        self::start();
        $_SESSION['__last_activity'] = time();
    }

    public static function getTimeoutInSeconds(): int
    {
        return self::timeoutInSeconds();
    }

    public static function isExpired(): bool
    {
        self::start();

        if (!isset($_SESSION['__last_activity'])) {
            return false;
        }

        return (time() - (int) $_SESSION['__last_activity']) >= self::timeoutInSeconds();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::start();
        session_unset();
        session_destroy();
    }

    public static function flash(string $key, mixed $value = null): mixed
    {
        self::start();
        if ($value !== null) {
            $_SESSION['__flash'][$key] = $value;
            $_SESSION['__flash_new'][$key] = true;
            return null;
        }
        return $_SESSION['__flash'][$key] ?? null;
    }

    public static function hasFlash(string $key): bool
    {
        self::start();
        return isset($_SESSION['__flash'][$key]);
    }

    public static function clearOldFlash(): void
    {
        self::start();
        if (isset($_SESSION['__flash'])) {
            foreach ($_SESSION['__flash'] as $key => $value) {
                if (!isset($_SESSION['__flash_new'][$key])) {
                    unset($_SESSION['__flash'][$key]);
                }
            }
        }
        $_SESSION['__flash_new'] = [];
    }
}

