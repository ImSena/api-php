<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use Exception;
use PDO;
use PDOException;

class Order extends BaseModel
{
    public function create(array $data)
    {
        try {

            $sql = "INSERT INTO orders (id_user, id_address";
            $values = "VALUES (:id_user, :id_address";

            if (!empty($data['id_coupon'])) {
                $sql .= ", id_coupon";
                $values .= ", :id_coupon";
            }

            $sql .= ") " . $values . ")";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":id_user", $data['id_user'], PDO::PARAM_INT);
            $stmt->bindParam(":id_address", $data['id_address'], PDO::PARAM_INT);

            if (!empty($data['id_coupon'])) {
                $stmt->bindParam(":id_coupon", $data['id_coupon'], PDO::PARAM_INT);
            }

            $stmt->execute();
            $orderId = $this->pdo->lastInsertId();

            if (empty($orderId)) {
                throw new Exception("Erro ao criar pedido");
            }

            $orderItems = $this->createOrderItems($data['order_items'], $orderId, $this->pdo);
            if (!$orderItems) {
                throw new Exception("Erro ao criar item do pedido");
            }

            $orderStatus = $this->createOrderStatus($orderId, $this->pdo);
            if (!$orderStatus) {
                throw new Exception("Erro ao criar status do pedido");
            }

            return $orderId;
        } catch (PDOException $e) {
            throw new Exception("Erro PDO na criação do pedido: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Erro geral na criação do pedido: " . $e->getMessage());
        }
    }
    private function createOrderItems(array $orderItems, int $orderId, $pdo)
    {

        if (!$this->hasSufficientStock($orderItems, $pdo)) {
            throw new Exception("Estoque insuficiente para um ou mais itens.");
        }

        $sql = "INSERT INTO order_item (id_order, id_product_variant, quantity) VALUES (:id_order, :id_product_variant, :quantity)";
        $stmt = $pdo->prepare($sql);
        foreach ($orderItems as $item) {
            $stmt->bindParam(":id_order", $orderId, PDO::PARAM_INT);
            $stmt->bindParam(":id_product_variant", $item['id_product_variant'], PDO::PARAM_INT);
            $stmt->bindParam(":quantity", $item['quantity'], PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() === 0) {
                return false;
            }
        }

        if (!$this->updateProductStock($orderItems, $pdo)) {
            return false;
        }
        return true;
    }
    private function hasSufficientStock(array $orderItems, $pdo)
    {
        $sql = "SELECT qtd_stock FROM product_variants WHERE id_product_variant = :id_product_variant";
        $stmt = $pdo->prepare($sql);

        foreach ($orderItems as $item) {
            $stmt->bindValue(":id_product_variant", $item['id_product_variant'], PDO::PARAM_INT);
            $stmt->execute();
            $stock = $stmt->fetchColumn();


            if ($stock === false || $stock < $item['quantity']) {
                return false;
            }
        }
        return true;
    }
    private function updateProductStock(array $orderItems, $pdo)
    {
        $sql = "UPDATE product_variants SET qtd_stock = qtd_stock - :quantity, updated_at = :updated_at WHERE id_product_variant = :id_product_variant";
        $stmt = $pdo->prepare($sql);

        foreach ($orderItems as $item) {
            $stmt->bindParam(":quantity", $item['quantity'], PDO::PARAM_INT);
            $stmt->bindParam(":id_product_variant", $item['id_product_variant'], PDO::PARAM_INT);
            $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                return false;
            }
        }
        return true;
    }
    private function createOrderStatus(int $orderId, $pdo)
    {
        $sql = "INSERT INTO order_status (id_order) VALUES (:id_order)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id_order", $orderId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            return false;
        }
        return true;
    }
    public function getAll(array $data)
    {

        if ($data['rule'] == 'user') {
            return $this->getOrdersUser($data, $this->pdo);
        } else {
            return $this->getOrders($data, $this->pdo);
        }
    }
    private function getOrdersUser(array $permissions, PDO $pdo)
    {

        $limit = 10;
        $page = isset($permissions['params']['page']) ? (int) $permissions['params']['page'] : 1;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT o.id_order, o.created_at AS order_created_at, o.id_address, o.id_user ,os.status, os.created_at AS status_created_at
            FROM orders o
            JOIN order_status os 
                ON os.id_order_status = (
                    SELECT id_order_status 
                    FROM order_status 
                    WHERE order_status.id_order = o.id_order 
                    ORDER BY created_at DESC 
                    LIMIT 1
                )
            WHERE o.id_user = :id
            ORDER BY
                o.created_at DESC
            LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $permissions['id_user'], PDO::PARAM_INT);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    private function getOrders(array $permissions, PDO $pdo)
    {
        $limit = 25;
        $page = isset($permissions['params']['page']) ? (int) $permissions['params']['page'] : 1;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT o.id_order, o.created_at AS order_created_at, o.id_address, o.id_user ,os.status, os.created_at AS status_created_at
            FROM orders o
            JOIN order_status os 
                ON os.id_order_status = (
                    SELECT id_order_status 
                    FROM order_status 
                    WHERE order_status.id_order = o.id_order 
                    ORDER BY created_at DESC 
                    LIMIT 1
                )
            ORDER BY
                o.created_at DESC
            LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    public function getTotalOrders($permissions)
    {


        if ($permissions['rule'] = 'user') {
            return $this->getTotalOrdersUser($permissions['id_user'], $this->pdo);
        } else {
            return $this->getTotalOrdersAdmin($this->pdo);
        }
    }
    private function getTotalOrdersUser(int $id_user, PDO $pdo)
    {
        $sql = "SELECT COUNT(id_order) AS total 
                FROM orders 
                WHERE id_user = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id_user, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }
    private function getTotalOrdersAdmin(PDO $pdo)
    {
        $sql = "SELECT COUNT(id_order) AS total 
                FROM orders";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id_user, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }
    public function getAllStatus(array $data)
    {

        $limit = 0;
        $sql = '';

        if ($data['rule'] == 'user') {
            $limit = 10;
            $sql = "SELECT o.id_order, o.created_at AS order_created_at, o.id_address, o.id_user ,os.status, os.created_at AS status_created_at
            FROM orders o
            JOIN order_status os 
                ON os.id_order_status = (
                    SELECT id_order_status 
                    FROM order_status 
                    WHERE order_status.id_order = o.id_order 
                    ORDER BY created_at DESC 
                    LIMIT 1
                )
            WHERE os.status = :status AND o.id_user = :id
            ORDER BY
                o.created_at DESC
            LIMIT :limit OFFSET :offset";
        } else if ($data['rule'] == 'admin') {
            $limit = 25;
            $sql = "SELECT o.id_order, o.created_at AS order_created_at, o.id_address, o.id_user ,os.status, os.created_at AS status_created_at
            FROM orders o
            JOIN order_status os 
                ON os.id_order_status = (
                    SELECT id_order_status 
                    FROM order_status 
                    WHERE order_status.id_order = o.id_order 
                    ORDER BY created_at DESC 
                    LIMIT 1
                )
            WHERE os.status = :status
            ORDER BY
                o.created_at DESC
            LIMIT :limit OFFSET :offset";
        }

        $page = isset($data['params']['page']) ? (int) $data['params']['page'] : 1;
        $offset = ($page - 1) * $limit;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(":status", $data['params']['status'], PDO::PARAM_STR);

        if ($data['rule'] == 'user') {
            $stmt->bindValue(":id", $data['id_user'], PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }
    public function getTotalStatus(array $data)
    {

        $limit = 0;
        $sql = '';

        if ($data['rule'] == 'user') {
            $limit = 10;
            $sql = "SELECT COUNT(o.id_order) AS total
            FROM orders o
            JOIN order_status os 
                ON os.id_order_status = (
                    SELECT id_order_status 
                    FROM order_status 
                    WHERE order_status.id_order = o.id_order 
                    ORDER BY created_at DESC 
                    LIMIT 1
                )
            WHERE os.status = :status AND o.id_user = :id
            ORDER BY
                o.created_at DESC
            LIMIT :limit OFFSET :offset";
        } else if ($data['rule'] == 'admin') {
            $limit = 25;
            $sql = "SELECT COUNT(o.id_order) AS total
            FROM orders o
            JOIN order_status os 
                ON os.id_order_status = (
                    SELECT id_order_status 
                    FROM order_status 
                    WHERE order_status.id_order = o.id_order 
                    ORDER BY created_at DESC 
                    LIMIT 1
                )
            WHERE os.status = :status
            ORDER BY
                o.created_at DESC
            LIMIT :limit OFFSET :offset";
        }

        $page = isset($data['params']['page']) ? (int) $data['params']['page'] : 1;
        $offset = ($page - 1) * $limit;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(":status", $data['params']['status'], PDO::PARAM_STR);

        if ($data['rule'] == 'user') {
            $stmt->bindValue(":id", $data['id_user'], PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetch();
    }

    public function getById(int $id)
    {
        $sql = "SELECT * FROM orders WHERE id_order = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderIdProductItems(int $id)
    {

        $sql = "SELECT id_product_variant, quantity FROM order_item WHERE id_order = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getQtdStatus(string $status){
        $sql = "SELECT COUNT(*) AS total FROM orders o 
        INNER JOIN ( 
            SELECT os1.id_order, os1.status FROM order_status os1 
            INNER JOIN ( 
                SELECT id_order, MAX(created_at) AS max_created_at FROM order_status GROUP BY id_order 
            ) os2 ON os1.id_order = os2.id_order AND os1.created_at = os2.max_created_at 
        ) latest_status ON o.id_order = latest_status.id_order 
        WHERE latest_status.status = :status";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(":status", $status, PDO::PARAM_STR);

        if(!$stmt->execute()){
            return false;
        }

        return $stmt->fetch();
    }

    public function changeStatus(string $status, int $id_order)
    {
        $sql = "INSERT INTO order_status (id_order, status) VALUES (:id_order, :status)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id_order', $id_order, PDO::PARAM_INT);
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function verifyStatus(int $id){
        $sql = "SELECT status FROM order_status WHERE id_order = :id ORDER BY id_order_status DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function insertPayment(array $data)
    {
        $sql = "UPDATE orders SET payment_url = :payment_url, payment_expires_at = :payment_expires_at, updated_at = :updated_at WHERE id_order = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":payment_url", $data['payment_url'], PDO::PARAM_STR);
        $stmt->bindParam(":payment_expires_at", $data['payment_expires_at'], PDO::PARAM_STR);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
        $stmt->bindParam(":id", $data['id'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getLinkPayment(int $id)
    {
        $sql = "SELECT payment_url, payment_expires_at FROM orders WHERE id_order = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }
}
