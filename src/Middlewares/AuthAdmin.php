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

        if(is_array($decoded) && isset($decoded['decoded']['error'])){
            $response::json([
                'success' => false,
                'message' => $decoded['error']
            ], 401);
            return false;
        }

        if(!isset($decoded['decoded']['rule']) && $decoded['decoded']['rule'] !== 'admin'){
            $response::json([
                'success' => false,
                'message' => "Acesso negado."
            ], 403);
            return false;
        }

        return true;
    }
}