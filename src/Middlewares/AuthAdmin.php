<?php

namespace App\Middlewares;

use App\Http\Request;
use App\Http\Response;
use App\Jwt\JwtAuth;

class AuthAdmin
{
    public function handle(Request $request, Response $response)
    {
        $token = $request::getToken();

        if (!$token) {
            return $this->denyAccess($response, 'Acesso negado.', 401);
        }

        $decoded = JwtAuth::verifyToken($token);

        if (isset($decoded['error'])) {
            return $this->denyAccess($response, $decoded['error'], 401);
        }

        $data = $decoded['decoded'] ?? [];

        if (($data['rule'] ?? '') !== 'admin') {
            return $this->denyAccess($response, 'Acesso negado.', 403);
        }

        if (empty($data['status']) || $data['status'] !== 'ACTIVE') {
            return $this->denyAccess($response, 'Admin precisa estar ativo!', 401);
        }

        return true;
    }

    private function denyAccess(Response $response, string $message, int $statusCode)
    {
        $response::json([
            'success' => false,
            'message' => $message
        ], $statusCode);
        return false;
    }
}