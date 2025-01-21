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

        if(!$token){
            $response::json([
                'success' => false,
                'message' => 'Token não encontrado ou inválido.'
            ], 401);
            return false;
        }

        $decoded = JwtAuth::verifyToken($token);

        if(is_array($decoded) && isset($decoded['erro'])){
            $response::json([
                'success' => false,
                'message' => $decoded['erro']
            ], 401);
            return false;
        }

        if(!isset($decode->rule) && $decoded->rule !== 'admin'){
            $response::json([
                'success' => false,
                'message' => "Acesso negado."
            ], 403);
            return false;
        }

        return true;
    }
}