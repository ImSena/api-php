<?php

namespace App\Middlewares;

use App\Http\Request;
use App\Http\Response;
use App\Jwt\JwtAuth;
use App\Middlewares\Base\BaseMiddleware;

class AuthPermission extends BaseMiddleware
{
    public function handle(Request $request, Response $response):bool
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

}