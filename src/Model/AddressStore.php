<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class AddressStore extends BaseModel
{
    public function createAddress(array $data): bool
    {

        $sql = "INSERT INTO address_store 
        (public_area, number, complement, district, city, state, zip_code, is_default, is_show)
        VALUES (:public_area, :number, :complement, :district, :city, :state, :zip_code, :is_default, :is_show)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":public_area", $data['public_area'], PDO::PARAM_STR);
        $stmt->bindParam(":number", $data['number'], PDO::PARAM_STR);
        $stmt->bindParam(":complement", $data['complement'], isset($data['complement']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(":district", $data['district'], PDO::PARAM_STR);
        $stmt->bindParam(":city", $data['city'], PDO::PARAM_STR);
        $stmt->bindParam(":state", $data['state'], PDO::PARAM_STR);
        $stmt->bindParam(":zip_code", $data['zip_code'], PDO::PARAM_STR);
        $stmt->bindParam(":is_default", $data['is_default'], PDO::PARAM_BOOL);
        $stmt->bindParam(":is_show", $data['is_show'], PDO::PARAM_BOOL);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function setIsDefault(bool $value = false)
    {
        $sql = "UPDATE address_store SET is_default = :value";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":value", $value, PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    public function getAddresses()
    {
        $sql = "SELECT 
        id_address_store,
        public_area, 
        number, 
        complement, 
        district, 
        city, 
        state, 
        zip_code, 
        is_default, 
        is_show 
        FROM address_store WHERE is_active > 0";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function updateStore(array $data)
    {
        $sql = "UPDATE address_store SET
        public_area = :public,
        number = :number,
        complement = :complement,
        district = :district,
        city = :city,
        state = :state,
        zip_code = :zip_code,
        is_default = :is_default,
        is_show = :is_show,
        updated_at = :updated_at
        WHERE id_address_store = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":public", $data['public_area'], PDO::PARAM_STR);
        $stmt->bindParam(":number", $data['number'], PDO::PARAM_STR);
        $stmt->bindParam(":complement", $data['complement'], PDO::PARAM_STR);
        $stmt->bindParam(":district", $data['district'], PDO::PARAM_STR);
        $stmt->bindParam(":city", $data['city'], PDO::PARAM_STR);
        $stmt->bindParam(":state", $data['state'], PDO::PARAM_STR);
        $stmt->bindParam(":zip_code", $data['zip_code'], PDO::PARAM_STR);
        $stmt->bindParam(":is_default", $data['is_default'], PDO::PARAM_BOOL);
        $stmt->bindParam(":is_show", $data['is_show'], PDO::PARAM_BOOL);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
        $stmt->bindParam(":id", $data['id'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
