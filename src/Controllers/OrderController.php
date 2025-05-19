<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;
use App\Service\OrderService;

class OrderController extends BaseController
{
    public function create()
    {
        $body = $this->request::body();
        $body['id_user'] = $this->request::getUserId();
    
        $orderService = new OrderService($this->pdo);
        $orderService = $orderService->create($body);

        if(isset($orderService['error'])){
            return $this->errorResponse($orderService['error']);
        }

        $this->response::json([
            "success" => true,
            "message" => $orderService['message'],
            "id_order" => $orderService['id_order']
        ]);
    }

    public function getAll($param)
    {

        $params = [];
        if(count($param) > 1){
            $params['status'] = isset($param[0]) ? $param[0] : "all";
            $params['page'] = isset($param[1]) ? intval($param[1]) : 1;
        }else{
            $params['page'] = isset($param[0]) ? intval($param[0]) : 1;
        }

        $data = [];
        $data['id_user'] = $this->request::getUserId();
        $data['rule'] = $this->request::getRule();
        $data['params'] = $params;

        $orderService = new OrderService($this->pdo);
        $orderService = $orderService->getAll($data);

        if(isset($orderService['error'])){
            return $this->errorResponse($orderService['error']);
        }

        $this->response::json([
            "success" => true,
            "message" => $orderService['message'],
            "content" => $orderService['content'],
            "page" => $orderService['page']
        ]);
    }

    public function getById($id)
    {
        $id = intval($id[0]);

        $orderService = new OrderService($this->pdo);
        $orderService = $orderService->getById($id);

        if(isset($orderService['error'])){
            return $this->errorResponse($orderService['error']);
        }

        return $this->successResponse($orderService['message'], $orderService['content']);
    }

    public function changeStatus($id)
    {
        $id = intval($id[0]);
        $body = $this->request::body();
        $files = $this->request::files();
        $status = $body['status'];
        
        $orderService = new OrderService($this->pdo);
        $orderService = $orderService->changeStatus($status, $id, $files);

        if(isset($orderService['error'])){
            return $this->errorResponse($orderService['error']);
        }

        return $this->successResponse("Status do pedido alterado com sucesso.");
    }

}