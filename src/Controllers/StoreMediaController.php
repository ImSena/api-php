<?php

namespace App\Controllers;
use App\Factory\ConnectionFactory;
use App\Http\Request;
use App\Http\Response;
use App\Service\StoreMediaService;
use PDO;


class StoreMediaController{
    
    private PDO $pdo;

    public function __construct(){
        $this->pdo = ConnectionFactory::getConnection();
    }

    public function create(Request $request, Response $response){
        
        $data = $request::body();

        $StoreMediaService = new StoreMediaService($this->pdo);
        $result = $StoreMediaService->createMedia($data);

        if(isset($result['error'])){
            return $response::json([
                "success" => false,
                "message" => $result['error']
            ], 400);
        }
        
        $response::json([
            "success" => true,
            "message" => $result
        ]);
    }

}