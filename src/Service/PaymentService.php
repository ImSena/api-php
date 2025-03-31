<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Utils\Validator;
use Exception;
use PDOException;

class PaymentService{
    public static function payOrder(array $data)
    {
        try{
            $fields = Validator::validate([
                'payment_method' => $data['payment_method'] ?? '',
            ]);
    
            $fields['id_order'] = $data['id_order'];
            
            $totalPrice = OrderService::getById($fields['id_order']);
            
            $totalPrice = $totalPrice['content']['total_price'];
    
            echo json_encode($totalPrice);
        }catch(PDOException $e){
            return ['error' => DatabaseErrorHelpers::error($e)];
        }
        catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

}