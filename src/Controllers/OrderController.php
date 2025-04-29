<?php

namespace App\Controllers;

use App\Factory\ConnectionFactory;
use App\Http\Request;
use App\Http\Response;
use App\Service\OrderService;
use PDO;

class OrderController
{

    private PDO $pdo;

    public function __construct(){
        $this->pdo = ConnectionFactory::getConnection();
    }
    public function create(Request $request, Response $response)
    {
        $body = $request::body();
        $body['id_user'] = $request::getUserId();
    
        $orderService = new OrderService($this->pdo);
        $orderService = $orderService->create($body);

        if(isset($orderService['error'])){
            return $response::json([
                'success' => false,
                "message" => $orderService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $orderService['message'],
            "id_order" => $orderService['id_order']
        ]);
    }

    public function getAll(Request $request, Response $response, $param)
    {

        $params = [];
        if(count($param) > 1){
            $params['status'] = isset($param[0]) ? $param[0] : "all";
            $params['page'] = isset($param[1]) ? intval($param[1]) : 1;
        }else{
            $params['page'] = isset($param[0]) ? intval($param[0]) : 1;
        }

        $data = [];
        $data['id_user'] = $request::getUserId();
        $data['rule'] = $request::getRule();
        $data['params'] = $params;

        $orderService = new OrderService($this->pdo);
        $orderService = $orderService->getAll($data);

        if(isset($orderService['error'])){
            return $response::json([
                'success' => false,
                "message" => $orderService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $orderService['message'],
            "content" => $orderService['content'],
            "page" => $orderService['page']
        ]);
    }

    public function getById(Request $request, Response $response, $id)
    {
        $id = intval($id[0]);

        $orderService = new OrderService($this->pdo);
        $orderService = $orderService->getById($id);

        if(isset($orderService['error'])){
            return $response::json([
                'success' => false,
                "message" => $orderService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $orderService['message'],
            "content" => $orderService['content']
        ]);
    }

}