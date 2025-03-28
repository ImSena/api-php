<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Coupon;
use App\Utils\Validator;
use Exception;
use PDOException;

class CouponService{
    public static function create(array $data)
    {
        try{

            $Coupon = new Coupon();

            $fields = Validator::validate([
                "name" => $data['name'] ?? '',
                "dicount" => $data['discount'] ?? '',
            ]);

            $Coupon = $Coupon->create($fields);

        }catch(PDOException $e){
            return ['error' => DatabaseErrorHelpers::error($e)];
        }catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    public static function getAll()
    {

    }

    public static function getById(int $id)
    {
        try{

            $Coupon = new Coupon();

            $Coupon = $Coupon->getCoupon($id);

            if(!$Coupon){
                throw new Exception("Não foi possivel resgatar cupom promocional");
            }

            return [
                'message' => "Cupom resgatado com sucesso.",
                'content' => $Coupon
            ];
        }catch(PDOException $e){
            return [
                'error' => DatabaseErrorHelpers::error($e)
            ];
        }catch(Exception $e){
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}