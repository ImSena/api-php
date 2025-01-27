<?php

namespace App\Helpers;

use Exception;
use PDOException;

class DatabaseErrorHelpers
{
    private static array $errorMessages = [
        '08006' => "Não foi possível se conectar ao banco de dados.",
        '23505' => "Usuário já cadastrado!",
        '1049' => "Banco de dados desconhecido.",
        '42S02' => "Tabela inexistente.",
        '42S22' => "Coluna inexistente.",
        '23000' => "Foi encontrado um valor duplicado!"
    ];

    public static function error(PDOException $e)
    {
        $errorCode = $e->getCode(); 
        $errorMessage = $e->getMessage(); 

        if ($errorCode && isset(self::$errorMessages[$errorCode])) {
            return self::$errorMessages[$errorCode];
        }

        return "Erro desconhecido: " . $errorMessage;
    }
}