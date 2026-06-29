<?php

namespace App\Middleware;

use App\Helpers\Security;

class CsrfMiddleware
{
    public function handle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_token'] ?? '';
            if (!Security::verifyCsrfToken($token)) {
                http_response_code(403);
                echo "403 - Token CSRF inválido";
                exit;
            }
        }
    }
}

