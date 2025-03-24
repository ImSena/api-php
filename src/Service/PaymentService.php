<?php

namespace App\Service;

use App\Utils\Validator;

class PaymentService{
    public static function payOrder(array $data)
    {

        $fields = Validator::validate([
            'payment_method' => $data['payment_method'] ?? '',
        ]);

        $fields['id_order'] = $data['id_order'];
        



    }

}