<?php

namespace App\Controllers\Frete;

use App\Factory\ConnectionFactory;
use App\Http\Request;
use App\Http\Response;
use PDO;

class ShippingController{

    private PDO $pdo;

    public function __construct(){
        $this->pdo = ConnectionFactory::getConnection();
    }

    public function getQuote(Request $request, Response $response){

        $body = Request::body();

        

    }
}