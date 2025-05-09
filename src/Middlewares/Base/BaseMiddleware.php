<?php

namespace App\Middlewares\Base;

use App\Http\Response;
use App\Interfaces\IMiddleware;
use PDO;

abstract class BaseMiddleware implements IMiddleware
{
    protected PDO $connection;

    public function __construct(PDO $con)
    {
        $this->connection = $con;
    }
    protected function denyAccess(Response $response, string $message, int $statusCode):bool
    {
        $response::json([
            'success' => false,
            'message' => $message
        ], $statusCode);

        return false;
    }
}