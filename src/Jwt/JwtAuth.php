<?php

namespace App\Jwt;

use Exception;
use Firebase\JWT\JWT as JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

define('SECRET_KEY', 'k!v9X3o5@zTmFc7cQ^wL5kE2bD8jZb0N');

class JwtAuth
{
    private static $secretKey = SECRET_KEY;

    public static function renderToken(string $name, $id, string $rule, string $expTime = '1 day'): string
    {
        $expTimeInSeconds = self::parseExpiration($expTime);

        $issuedAt = time();
        $expirationTime = $issuedAt + $expTimeInSeconds;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'id_user' => $id,
            'name' => $name,
            'rule' => $rule
        ];

        $jwt = JWT::encode($payload, self::$secretKey, 'HS256');

        return $jwt;
    }

    public static function verifyToken(string $token)
    {
        try {

            $decoded = JWT::decode($token, new Key(self::$secretKey, 'HS256'));

            return (object) $decoded;
        } catch (ExpiredException $e) {
            return ['erro' => 'Token expirado. ' . $e->getMessage()];
        } catch (SignatureInvalidException $e) {
            return ['erro' => 'Assinatura inválida. ' . $e->getMessage()];
        } catch (BeforeValidException $e) {
            return ['erro' => "Token ainda não válido. " . $e->getMessage()];
        } catch (Exception $e) {
            return ['erro' => $e->getMessage()];
        }
    }

    private static function parseExpiration(string $expTime): int
    {
        preg_match('/(\d+)\s*(\w+)/', $expTime, $matches);

        if (empty($matches)) {
            return 0;
        }

        $value = (int) $matches[1];
        $unit = strtolower($matches[2]);

        switch($unit){
            case 'second':
            case 'seconds':
                return $value;
            case 'minute':
            case 'minutes':
                return $value * 60;
            case 'hour':
            case 'hours': 
                return $value * 3600;
            case 'day':
            case 'days':
                return $value * 86400;
            default: 
                return 0;
        }
    }
}
