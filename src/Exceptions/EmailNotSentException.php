<?php

namespace App\Exceptions;

use Exception;

class EmailNotSentException extends Exception
{
    public function __construct(string $message = "Desculpe, rota não encontrada", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}