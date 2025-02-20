<?php

namespace App\Jwt;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

define('SECRET_KEY', 'k!v9X3o5@zTmFc7cQ^wL5kE2bD8jZb0N');

class JwtAuth
{
    private static $secretKey = SECRET_KEY;

    public static function renderToken(string $name, $id, string $rule, string $status, string $expTime = '1 day'): string
    {
        $expTimeInSeconds = self::parseExpiration($expTime);
        $issuedAt = time();
        $expirationTime = $issuedAt + $expTimeInSeconds;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'id_user' => $id,
            'name' => $name,
            'rule' => $rule,
            'status' => $status
        ];

        return JWT::encode($payload, trim(self::$secretKey), 'HS256');
    }

    public static function verifyToken(string $token)
    {
        try {
            return ['decoded' => (array) JWT::decode($token, new Key(self::$secretKey, 'HS256'))];
        } catch (ExpiredException $e) {
            return ['error' => 'Token expirado.'];
        } catch (SignatureInvalidException $e) {
            return ['error' => 'Assinatura inválida.'];
        } catch (BeforeValidException $e) {
            return ['error' => 'Token ainda não válido.'];
        } catch (Exception $e) {
            return ['error' => 'Erro ao processar token: ' . $e->getMessage()];
        }
    }

    private static function parseExpiration(string $expTime): int
    {
        if (preg_match('/(\d+)\s*(\w+)/', $expTime, $matches)) {
            $value = (int) $matches[1];
            $unit = strtolower($matches[2]);

            return match ($unit) {
                'second', 'seconds' => $value,
                'minute', 'minutes' => $value * 60,
                'hour', 'hours' => $value * 3600,
                'day', 'days' => $value * 86400,
                default => 0,
            };
        }
        return 0;
    }
}