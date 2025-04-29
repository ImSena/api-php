<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\StoreMedia;
use App\Utils\Validator;
use Exception;
use PDO;
use PDOException;

class StoreMediaService{
    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function createMedia(array $data){
        try{

            $this->pdo->beginTransaction();

            $types = [
                'LOGO',
                'LOGO_FOOTER',
                'FAVICON',
            ];

            $fields = Validator::validate([
                "id_media" => $data['id_media'],
                "type" => $data['type'],
            ]);

            if(!in_array($fields['type'], $types)){
                throw new Exception("Tipo de midia inválido.");
            }

            $StoreMedia = new StoreMedia($this->pdo);
            
            $StoreMedia->inactiveMedia($fields);

            if (!$StoreMedia->create($fields)) {
                throw new Exception("Não foi possível inserir a mídia.");
            }

            $this->pdo->commit();

            return "Media criada com sucesso";

        }catch(PDOException $e){
            $this->pdo->rollBack();
            return ['error' => DatabaseErrorHelpers::error($e)];
        }catch(Exception $e){
            $this->pdo->rollBack();
            return ['error' => $e->getMessage()];
        }
    }
}