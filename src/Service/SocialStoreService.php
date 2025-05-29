<?php

namespace App\Service;

use App\Model\SociaisStore;
use App\Service\Base\BaseService;
use App\Utils\Validator;
use Exception;

class SocialStoreService extends BaseService
{
    private $ITypeSociais = [
        "INSTAGRAM",
        "FACEBOOK",
        "YOUTUBE",
        "LINKEDIN",
        "TIKTOK",
        "X"
    ];

    public function createSocial(array $data, bool $isTransaction = false)
    {
        return $this->execute(function () use ($data) {

            $fields = Validator::validate([
                "type" => $data['type'] ?? '',
                "link" => $data['link'] ?? ''
            ]);

             if (!in_array($fields['type'], $this->ITypeSociais)) {
                $types = implode(", ", $this->ITypeSociais);
                throw new Exception("Tipo de phone está incorreto! Tipos permitidos [" . $types . "]");
            }

            $SocialStore = new SociaisStore($this->pdo);

            $find = $SocialStore->findByType($data['type']);

            if ($find) {
                throw new Exception("Midia social já cadastrada");
            }

            $result = $SocialStore->createSociais($data);

            if (!$result) {
                throw new Exception("Não foi possível cadastrar rede social.");
            }

            return "Rede social cadastrada com sucesso.";
        }, $isTransaction);
    }

    public function getSocial()
    {
        return $this->execute(function () {
            $SocialStore = new SociaisStore($this->pdo);

            $result = $SocialStore->getSociais();

            if (!$result) {
                throw new Exception("Não foi possível resgatar sociais midias.");
            }

            return $result;
        });
    }

    public function update(array $data)
    {
        return $this->execute(function () use ($data) {

            $fields = Validator::validate([
                "type" => $data['type'] ?? '',
                "link" => $data['link'] ?? ''
            ]);

            $fields['type'] = strtoupper($fields['type']);

            if (!in_array($fields['type'], $this->ITypeSociais)) {
                $types = implode(", ", $this->ITypeSociais);
                throw new Exception("Tipo de phone está incorreto! Tipos permitidos [" . $types . "]");
            }
            $SocialStore = new SociaisStore($this->pdo);

            $result = $SocialStore->getSocial(strtoupper($fields['type']));

            if(!$result){
                throw new Exception("Não foi possível encontrar a rede social");
            }
            
            $result = $SocialStore->updateSocial($data);

            if (!$result) {
                throw new Exception("Não foi possível atualizar sociais midias.");
            }

            return "Social Midia atualizada com sucesso.";
        });
    }

    public function delete(string $type)
    {
        return $this->execute(function() use ($type){

            $type = strtoupper($type);

            if (!in_array($type, $this->ITypeSociais)) {
                $types = implode(", ", $this->ITypeSociais);
                throw new Exception("Tipo de phone está incorreto! Tipos permitidos [" . $types . "]");
            }

            $StoreSocial = new SociaisStore($this->pdo);

            $result = $StoreSocial->getSocial($type);

            if(!$result){
                throw new Exception("Não foi possível encontrar rede social");
            }

            $resultDelete = $StoreSocial->delete($type);

            if(!$resultDelete){
                throw new Exception("Não foi possível deletar a social midia");
            }

            return "Social midia deletada com sucesso.";

        });
    }
}
