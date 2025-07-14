<?php

namespace App\Service;

use App\Model\Email;
use App\Service\Base\BaseService;
use App\Utils\Validator;
use Exception;

class EmailService extends BaseService
{

    public function sendEmailStore(array $data)
    {
        return $this->execute(function() use ($data){

            $fields = Validator::validate([
                "name" => $data['name'] ?? '',
                "email" => $data['email'] ?? '',
                "phone" => $data['phone'] ?? '',
                "message" => $data['message'] ?? ''
            ]);

            $notification = new NotificationsService($this->pdo);

            $resultSent = $notification->sendNotificationStore($fields);

            if(isset($resultSent['error'])){
                throw new Exception("Não foi possível enviar e-mail");
            }

            return true;
        });
    }

}