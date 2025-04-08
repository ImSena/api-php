<?php

namespace App\Exceptions;

use Exception;

class RouteNotFoundException extends Exception
{
    public function __construct(string $message = "Desculpe, rota não encontrada", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}