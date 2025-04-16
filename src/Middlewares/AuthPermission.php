<?php

namespace App\Middlewares;

use App\Http\Request;
use App\Http\Response;
use App\Jwt\JwtAuth;

class AuthPermission
{
    public function handle(Request $request, Response $response)
    {
        $token = $request::getToken();

        if (!$token) {
            return self::denyAccess($response, 'Acesso negado.', 401);
        }

        $decoded = JwtAuth::verifyToken($token);

        if (isset($decoded['error'])) {
            return $this->denyAccess($response, $decoded['error'], 401);
        }

        $data = $decoded['decoded'] ?? [];


        if($data['rule'] == 'admin'){
            $authAdmin = new AuthAdmin();
            return $authAdmin->handle($request, $response);
        }else if($data['rule'] == 'user'){
            $authUser = new AuthUser();
            return $authUser->handle($request, $response);
        }else{
            return $this->denyAccess($response, 'Acesso negado.', 403);
        }

        if (($data['rule'] ?? '') !== 'user') {
            return $this->denyAccess($response, 'Acesso negado.', 403);
        }
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