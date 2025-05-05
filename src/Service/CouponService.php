<?php

namespace App\Service;

use App\Model\Coupon;
use App\Service\Base\BaseService;
use App\Utils\Validator;
use Exception;

class CouponService extends BaseService
{

    public function create(array $data)
    {
        return $this->execute(function() use ($data){
            $Coupon = new Coupon($this->pdo);
    
            $fields = Validator::validate([
                "name" => $data['name'] ?? '',
                "dicount" => $data['discount'] ?? '',
            ]);
    
            $Coupon = $Coupon->create($fields);
        });
    }

    public function getAll() {}

    public function getById(int $id)
    {
        return $this->execute(function() use ($id){
            $Coupon = new Coupon($this->pdo);
    
            $Coupon = $Coupon->getCoupon($id);
    
            if (!$Coupon) {
                throw new Exception("Não foi possivel resgatar cupom promocional");
            }
    
            return [
                'message' => "Cupom resgatado com sucesso.",
                'content' => $Coupon
            ];
        });
    }
}
