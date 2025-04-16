<?php

namespace App\Controllers;

use App\Factory\ConnectionFactory;
use App\Http\Request;
use App\Http\Response;
use App\Service\WebhookService;
use PDO;

class WebhookController
{
    private PDO $pdo;

    public function __construct(){
        $this->pdo = ConnectionFactory::getConnection();
    }
    public function getEvent(Request $request, Response $response)
    {
        $body = $request::body();
        $headers = $request::getHeaders();

        $webHookService = new WebhookService($this->pdo);
        $webHookService = $webHookService->processEvent($body, $headers);


        error_log("pagamento: ".print_r($webHookService, true));

        if(isset($webHookService['error'])){
            return $response::json([
                'success' => false,
                "message" => $webHookService['error']
            ], 400);
        }

        $response::json([
            'success' => true,
            'message' => $webHookService
        ]);
    }
}