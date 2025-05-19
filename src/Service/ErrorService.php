<?php

namespace App\Service;

use App\Model\Errors;
use App\Service\Base\BaseService;
use Exception;

class ErrorService extends BaseService
{
    public function register(array $data)
    {
        return $this->execute(function () use ($data) {
            $errorModel = new Errors($this->pdo);
            $emailStoreService = new EmailStoreService($this->pdo);

            $emailStore = $emailStoreService->getEmailDefault();

            if(isset($emailStore['error'])){
                throw new Exception("Não foi possível resgatar email");
            }

            $emailStore = $emailStore['email'];

            $result = $errorModel->register($data);

            if(!$result){
                throw new Exception("Não foi possível salvar erro no banco");
            }

            $this->getNotifier()->sendError(["info_error"=>$data['context']], $emailStore);

            return true;
        }, true);
    }
}
