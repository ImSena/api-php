<?php

namespace App\Service;

use App\Jwt\JwtAuth;
use App\Model\OrderShipping;
use App\Service\Base\BaseService;
use Exception;

class OrderShippingService extends BaseService
{
    public function createShipping(array $data)
    {
        return $this->execute(function() use ($data){
            $decoded = JwtAuth::verifyToken($data['shipping_signature']);
            
            if(isset($decoded['error'])){
                throw new Exception("Assinatura da cotação de frete inválida");
            }
    
            $OrderShipping = new OrderShipping($this->pdo);
    
            $shipping = $decoded['decoded']['shipping'];
            $shipping = json_decode($shipping, true);
    
            if(isset($shipping['error'])){
                throw new Exception($shipping['error']);
            }
    
            $dataShipping = [
                "id_order" => $data['id_order'],
                "carrier" => $shipping['company']['name'],
                "shipping_type" => $shipping['name'],
                "shipping_cost" => $shipping['price']
            ];
    
            $result = $OrderShipping->createShipping($dataShipping);
    
            if(!$result){
                throw new Exception("Não foi possível salvar a cotação do pedido.");
            }
    
            return "Cotação salva com sucesso.";

        }, true);
    }

    public function getOrderShipping(int $order)
    {
        return $this->execute(function() use ($order){
            $OrderShipping = new OrderShipping($this->pdo);
    
            $result = $OrderShipping->getOrderShipping($order);
    
            if(!$result){
                throw new Exception("Não foi possível resgatar frete do pedido.");
            }
    
            return $result;
        });

    }
}