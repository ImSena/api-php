<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;
use App\Notifications\NotificationsManager;
use App\Service\EmailStoreService;
use App\Service\ErrorService;
use App\Service\WebhookService;

class WebhookController extends BaseController
{
    public function getEvent()
    {
        $body = $this->request::body();
        $headers = $this->request::getHeaders();

        $webHookService = new WebhookService($this->pdo);
        $webHookService = $webHookService->processEvent($body, $headers);


        error_log("pagamento: " . print_r($webHookService, true));
        error_log("mensagem");

        if (isset($webHookService['error'])) {
            $errorService = new ErrorService($this->pdo);
            $NotificationManager = new NotificationsManager($this->pdo);
            $email = new EmailStoreService($this->pdo);
            $emailAdmin = $email->getEmailDefault();

            if (isset($emailAdmin['error'])) {
                $send = $NotificationManager->getDefaultNotifier()->sendError(["info_error" => $webHookService['error']], "suporte@escalaweb.com.br");
                if(!$send){
                    return $this->errorResponse("Ocorreu um erro ao enviar e-email");
                }else{
                    return $this->successResponse("email enviado.");
                }
            }

            $errorService->register([
                'source' => 'email_notification',
                'context' => json_encode([
                    $webHookService['error']
                ]),
                'message' => $webHookService['error'],
                'payload' => json_encode($webHookService)
            ]);

            $send = $NotificationManager->getDefaultNotifier()->sendError(["info_error" => $webHookService['error']], "");

            return $this->errorResponse($webHookService['error']);
        }

        return $this->successResponse($webHookService);
    }
}
