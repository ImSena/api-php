<?php

namespace App\Controllers\Base;

use App\Factory\ConnectionFactory;
use App\Http\Request;
use App\Http\Response;
use PDO;

abstract class BaseController
{
    protected ?PDO $pdo;
    protected Response $response;

    protected Request $request;

    public function __construct(Request $request, Response $response, ?PDO $connection = null)
    {
        $this->pdo = $connection;
        $this->request = $request;
        $this->response = $response;

    }

    protected function successResponse(string $message = 'Operação realizada com sucesso', array|string $data = [], int $status = 200)
    {
        $res = ['success' => true];

        if (is_string($data)) {
            $res['message'] = $data;
        } else {
            $res['message'] = $message;
            if (!empty($data)) {
                $res['content'] = $data;
            }
        }

        return $this->response::json($res, $status);
    }
    protected function errorResponse(string $message = 'Ocorreu um erro', int $status = 400)
    {
        return $this->response::json([
            'success' => false,
            'message' => $message
        ], $status);
    }
}
