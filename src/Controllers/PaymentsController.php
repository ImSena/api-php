<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;
use App\Service\PaymentService;

class PaymentsController extends BaseController
{
    public function pay($param)
    {
        $params['id_order'] = isset($param[0]) ? (int) $param[0] : null;
        $body = $this->request::body();
        $body['id_order'] = $params['id_order'];

        if(!isset($params['id_order'])){
            return $this->errorResponse("Por favor, informe o id do pagamento.");
        }

        $paymentService = new PaymentService($this->pdo);
        $paymentService = $paymentService->payOrder($body);

        if(isset($paymentService['error'])){
            return $this->errorResponse($paymentService['error']);
        }

        $this->response::json([
            "success" => true,
            "message" => $paymentService['message'],
            "session_url" => $paymentService['session_url']
        ]);
    }

    public function getPayments($param)
    {

    }

    public function getDetails($param)
    {

    }
}