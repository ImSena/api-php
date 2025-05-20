<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use Exception;
use PDO;
use PDOException;

class Address extends BaseModel
{
    public function create($data)
    {
        if ($data['is_default']) {
            $inactive = self::setDefault($this->pdo, $data['id_user'], false);

            if (!$inactive) {
                throw new Exception("Erro ao desativar endereço padrão");
            }
        }

        $sql = "INSERT INTO addresses (id_user, public_area, number, complement, district, city, state, zip_code) VALUES (:id_user, :public_area, :number, :complement, :district, :city, :state, :zip_code)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id_user", $data['id_user'], PDO::PARAM_INT);
        $stmt->bindParam(":public_area", $data['public_area'], PDO::PARAM_STR);
        $stmt->bindParam(":number", $data['number'], PDO::PARAM_STR);
        $stmt->bindParam(":complement", $data['complement'], empty($data['complement']) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":district", $data['district'], PDO::PARAM_STR);
        $stmt->bindParam(":city", $data['city'], PDO::PARAM_STR);
        $stmt->bindParam(":state", $data['state'], PDO::PARAM_STR);
        $stmt->bindParam(":zip_code", $data['zip_code'], PDO::PARAM_STR);
        $stmt->bindParam(":is_default", $data['is_default'], PDO::PARAM_BOOL);
        $stmt->execute();

        $addressId = $this->pdo->lastInsertId();

        if (empty($addressId)) {
            throw new Exception("Erro ao criar endereço");
        }

        return $addressId;
    }

    public function edit(array $data)
    {

        if ($data['is_default']) {
            $inactive = self::setDefault($this->pdo, $data['id_user'], false);

            if (!$inactive) {
                throw new Exception("Erro ao desativar endereço padrão");
            }
        }

        $sql = "UPDATE addresses SET 
        public_area = :public_area,
        number = :number,
        complement = :complement,
        district = :district,
        city = :city,
        state = :state,
        zip_code = :zip_code,
        is_default = :is_default
        WHERE id_user = :id_user AND id_address = :id_address
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":public_area", $data['public_area'], PDO::PARAM_STR);
        $stmt->bindParam(":number", $data['number'], PDO::PARAM_STR);
        $stmt->bindParam(":complement", $data['complement'], empty($data['complement']) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":district", $data['district'], PDO::PARAM_STR);
        $stmt->bindParam(":city", $data['city'], PDO::PARAM_STR);
        $stmt->bindParam(":state", $data['state'], PDO::PARAM_STR);
        $stmt->bindParam(":zip_code", $data['zip_code'], PDO::PARAM_STR);
        $stmt->bindParam(":is_default", $data['is_default'], PDO::PARAM_BOOL);
        $stmt->bindParam(":id_user", $data['id_user'], PDO::PARAM_INT);
        $stmt->bindParam(":id_address", $data['id_address'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    private function setDefault(PDO $pdo, int $id_user, bool $is_default = false, $id = false)
    {
        $sql = "UPDATE addresses SET is_default = :is_default, updated_at = :updated_at WHERE id_user = :id";

        if ($id) {
            $sql .= " AND id = :id";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":is_default", $is_default, PDO::PARAM_BOOL);
        $stmt->bindParam(":id", $id_user, PDO::PARAM_INT);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
        if ($id) {
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        }
        
        return $stmt->execute();
    }

    public function getAll(array $data)
    {
        if($data['rule'] == "admin"){
            $sql = "SELECT a.id_address, 
            a.public_area, 
            a.id_user,
            a.number, 
            a.complement, 
            a.district, 
            a.city, 
            a.state, a.zip_code, a.is_default FROM addresses AS a";
        }else{
            $sql = "SELECT 
            a.id_address,
            a.public_area, 
            a.number, 
            a.complement, 
            a.district, 
            a.city, 
            a.state, 
            a.zip_code, a.is_default FROM addresses AS a WHERE id_user = :id";
        }
        $stmt = $this->pdo->prepare($sql);
        if($data['rule'] !== 'admin'){
            $stmt->bindParam(":id", $data['id_user'], PDO::PARAM_INT);
        }
        $stmt->execute();

        $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $addresses;
    }

    public function getById(int $id)
    {

        $sql = "SELECT id_user, public_area, number, complement, district, city, state, zip_code FROM addresses WHERE id_address = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getByUser(int $id){
        $sql = "SELECT public_area, number, complement, district, city, state, zip_code, is_default FROM addresses WHERE id_user = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }
}
