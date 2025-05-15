<?php

namespace App\Middlewares;

use App\Http\Request;
use App\Http\Response;
use App\Middlewares\Base\BaseMiddleware;
use App\Service\StoreService;
use Exception;
use PDOException;

class LockedStore extends BaseMiddleware{


    public function handle(Request $request, Response $response):bool
    {
        return true;
        try{

            $storeService = new StoreService($this->connection);

            $result = $storeService->getStatus();

            if(isset($result['error'])){
                throw new Exception("Erro ao recuparar status da loja.");
            }

            if($result['is_locked']){
                $reasons = implode(",", $result['locked_reasons']);
                $reasons = str_replace(".", "", $reasons);

                return $this->denyAccess($response, "Loja incompleta para realizar requisição. Motivos: ".$reasons, 400);
            }

            return true;

        }catch(PDOException){
           return $this->denyAccess($response, "Erro ao recuperar status", 400);
        }catch(Exception){
            return $this->denyAccess($response, "Erro ao recuperar status da loja", 400);
        }
    }
}