<?php

namespace App\Jwt;

use Firebase\JWT\JWT as JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

define('SECRET_KEY', 'k!v9X3o5@zTmFc7cQ^wL5kE2bD8jZb0N');

class JwtAuth
{
    private static $secretKey = SECRET_KEY;

    public static function renderToken(string $name, $id, int $expTimeHours = 168):string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + ($expTimeHours * 3600);

        $payload = [
            'iat' => $issuedAt,    
            'exp' => $expirationTime,  
            'id_user' => $id,  
            'name' => $name 
        ];

        $jwt = JWT::encode($payload, self::$secretKey, 'HS256');

        return $jwt;
    }
}
