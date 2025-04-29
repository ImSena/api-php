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
        $pdo = $this->getPdo();
        $pdo->beginTransaction();

        try {
            $inactive = self::setDefault($pdo, $data['id_user'], false);

            if (!$inactive) {
                throw new Exception("Erro ao desativar endereço padrão");
            }

            $sql = "INSERT INTO addresses (id_user, public_area, number, complement, district, city, state, zip_code) VALUES (:id_user, :public_area, :number, :complement, :district, :city, :state, :zip_code)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":id_user", $data['id_user'], PDO::PARAM_INT);
            $stmt->bindParam(":public_area", $data['public_area'], PDO::PARAM_STR);
            $stmt->bindParam(":number", $data['number'], PDO::PARAM_STR);
            $stmt->bindParam(":complement", $data['complement'], PDO::PARAM_STR);
            $stmt->bindParam(":district", $data['district'], PDO::PARAM_STR);
            $stmt->bindParam(":city", $data['city'], PDO::PARAM_STR);
            $stmt->bindParam(":state", $data['state'], PDO::PARAM_STR);
            $stmt->bindParam(":zip_code", $data['zip_code'], PDO::PARAM_STR);
            $stmt->execute();

            $addressId = $pdo->lastInsertId();

            if (empty($addressId)) {
                throw new Exception("Erro ao criar endereço");
            }

            $pdo->commit();
            return $addressId;
        } catch (PDOException $e) {
            $pdo->rollBack();
            return false;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
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
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getAll(int $id)
    {
        $pdo = $this->getPdo();

        $sql = "SELECT id_address, public_area, number, complement, district, city, state, zip_code, is_default FROM addresses WHERE id_user = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $addresses;
    }

    public function getById(int $id)
    {
        $pdo = $this->getPdo();

        $sql = "SELECT id_user, public_area, number, complement, district, city, state, zip_code FROM addresses WHERE id_address = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }
}
