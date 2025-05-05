<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;
use App\Service\WebhookService;

class WebhookController extends BaseController
{
    public function getEvent()
    {
        $body = $this->request::body();
        $headers = $this->request::getHeaders();

        $webHookService = new WebhookService($this->pdo);
        $webHookService = $webHookService->processEvent($body, $headers);


        error_log("pagamento: ".print_r($webHookService, true));

        if(isset($webHookService['error'])){
            return $this->errorResponse($webHookService['error']);
        }

        return $this->successResponse($webHookService);
    }
}