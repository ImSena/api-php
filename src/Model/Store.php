<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use PDO;

class Store extends BaseModel
{

    public function createStore(array $data)
    {
        $sql = "INSERT INTO 
        store (name, domain, token_shipping, template, pallete, id_analitycs, id_search_console, id_tag_manager) 
        VALUES (:name, :domain, :token_shipping, :template, :pallete, :id_analitycs, :id_search_console, :id_tag_manager)";

        $token_shipping = isset($data['token_shipping']) ? $data['token_shipping'] : null;
        $id_analitycs = isset($data['id_analitycs']) ? $data['id_analitycs'] : null;
        $id_search_console = isset($data['id_search_console']) ? $data['id_search_console'] : null;
        $id_tag_manager = isset($data['id_tag_manager']) ? $data['id_tag_manager'] : null;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":name", $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(":domain", $data['domain'], PDO::PARAM_STR);
        $stmt->bindParam(":token_shipping", $token_shipping, $token_shipping === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":template", $data['template'], PDO::PARAM_STR);
        $stmt->bindParam(":pallete", $data['pallete'], PDO::PARAM_STR);
        $stmt->bindParam(":id_analitycs", $id_analitycs, $id_analitycs === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":id_search_console", $id_search_console, $id_search_console === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(":id_tag_manager", $id_tag_manager, $id_tag_manager === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
    public function setStatuStores($value = false)
    {
        $sql = "UPDATE store SET is_active = :value";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":value", $value, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    public function getActiveStore()
    {
        $sql = "SELECT id_store, name, domain, stripe_account_id, token_shipping, template, pallete, id_analitycs, id_search_console FROM store WHERE is_active > 0 LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function findStores()
    {
        $sql = "SELECT id_store, name, domain, stripe_account_id FROM store";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findById(string $storeId)
    {        $sql = "SELECT 
                    id_store, 
                    name, 
                    domain, 
                    stripe_account_id, 
                    (SELECT email FROM admins WHERE permission = 'SUPER' LIMIT 1) as email
                FROM store 
                WHERE id_store = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id", $storeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function updateAccount(array $data)
    {        $sql = "UPDATE store SET stripe_account_id = :account_id, updated_at = :updated_at WHERE is_active > 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":account_id", $data["stripe_account_id"], PDO::PARAM_STR);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getInfoStore()
    {        $sql = "SELECT name, domain, template, pallete, id_analytics, id_search_console, id_tag_manager FROM store WHERE stripe_account_id IS NOT NULL AND is_active > 0 ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getAddress()
    {        $sql = "SELECT id_address_store, public_area, number, complement, district, city, state, zip_code, is_default FROM address_store WHERE is_active > 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function updateStore(array $data): bool
    {
        $fields = [];
        $params = [];

        if (!empty($data['name'])) {
            $fields[] = "name = :name";
            $params[':name'] = $data['name'];
        }
        if (!empty($data['id_analitycs'])) {
            $fields[] = "id_analitycs = :analitycs";
            $params[':analitycs'] = $data['id_analitycs'];
        }
        if (!empty($data['id_search_console'])) {
            $fields[] = "id_search_console = :console";
            $params[':console'] = $data['id_search_console'];
        }
        if (!empty($data['id_tag_manager'])) {
            $fields[] = "id_tag_manager = :manager";
            $params[':manager'] = $data['id_tag_manager'];
        }

        if (empty($fields)) {
            return true;
        }

        $sql = "UPDATE store SET " . implode(', ', $fields) . " WHERE is_active > 0";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($params);
    }
}
