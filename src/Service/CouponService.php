<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Coupon;
use App\Utils\Validator;
use Exception;
use PDO;
use PDOException;

class CouponService{

    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }
    public function create(array $data)
    {
        try{

            $Coupon = new Coupon($this->pdo);

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

    public function getAll()
    {

    }

    public function getById(int $id)
    {
        try{

            $Coupon = new Coupon($this->pdo);

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