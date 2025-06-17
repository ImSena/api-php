<?php

namespace App\Middlewares;

use App\Http\Request;
use App\Http\Response;
use App\Jwt\JwtAuth;
use App\Middlewares\Base\BaseMiddleware;

class HasAdmin extends BaseMiddleware{
   public function handle(Request $request, Response $response): bool
   {

        $token = $request::getToken();

        if(!$token){
            $request::setRule("COMMON");
            return true;
        }

        $decoded = JwtAuth::verifyToken($token);

        if (isset($decoded['error'])) {
            return $this->denyAccess($response, $decoded['error'], 401);
        }

        $data = $decoded['decoded'] ?? [];


        if($data['rule'] == 'admin'){
            $authAdmin = new AuthAdmin();
            $request::setRule("AMDIN");
            return $authAdmin->handle($request, $response);
        }else{
            $request::setRule("COMMON");
        }
        return true;
    }
}