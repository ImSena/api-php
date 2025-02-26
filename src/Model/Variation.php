<?php

namespace App\Model;

use PDO;

class Variation extends Database
{
    public static function createVariation(array $data):bool
    {
        $pdo = self::getConnection();

        $sql = "INSERT INTO VARIANT_ATTRIBUTES (name) VALUES (:name)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function getAllVariants():array 
    {
        $pdo = self::getConnection();

        $sql = "SELECT id_variant_attribute, name FROM VARIANT_ATTRIBUTES";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function updateVariation(array $data):bool
    {
        $pdo = self::getConnection();

        $sql = "UPDATE VARIANT_ATTRIBUTES SET name = :name WHERE id_variant_attribute = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(":id", $data['id'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function deleteVariation(int $id):bool
    {
        $pdo = self::getConnection();

        $sql = "DELETE FROM VARIANT_ATTRIBUTES WHERE id_variant_attribute = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function createValue(array $data):bool
    {
        $pdo = self::getConnection();

        $sql = "INSERT INTO variant_attributes_values (id_variant_attribute, value, viewer) VALUES (:id_variant_attribute, :value, :viewer)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_variant_attribute", $data['id_variant_attribute'], PDO::PARAM_INT);
        $stmt->bindParam(":value", $data['value'], PDO::PARAM_STR);
        $stmt->bindParam(":viewer", $data['viewer'], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function getValuesVariation(int $id):array
    {
        $pdo = self::getConnection();

        $sql = "SELECT id_variant_attribute_value, value, viewer FROM VARIANT_ATTRIBUTES_VALUES WHERE id_variant_attribute = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function updateValue(array $data):bool
    {
        $pdo = self::getConnection();

        $sql = "UPDATE VARIANT_ATTRIBUTES_VALUES SET value = :value WHERE id_variant_attribute_value = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":value", $data['value'], PDO::PARAM_STR);
        $stmt->bindParam(":id", $data['id'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function deleteValue(int $id):bool
    {
        $pdo = self::getConnection();

        $sql = "DELETE FROM VARIANT_ATTRIBUTES_VALUES WHERE id_variant_attribute_value = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}