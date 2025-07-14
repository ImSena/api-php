<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;
use App\Service\EmailService;

class EmailController extends BaseController{

    public function sendMailStore()
    {
        $body = $this->request::body();
        
        $emailService = new EmailService($this->pdo);
        $result = $emailService->sendEmailStore($body);

        if(isset($result['error'])){
            return $this->errorResponse("Não foi possível enviar e-mail. Por favor, tente mais tarde");
        }

        return $this->successResponse("Email enviado com sucesso");
    }
}