<?php

namespace App\Controllers\Frete;

use App\Controllers\Base\BaseController;
use App\Service\Frete\ShippingService;

class ShippingController extends BaseController
{
    public function getQuote()
    {

        $body = $this->request::body();

        $shippingService = new ShippingService($this->pdo);

        $result = $shippingService->getQuote($body);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error']);
        }

        return $this->successResponse("Cotação de frete resgatada com sucesso", $result);
    }
}
