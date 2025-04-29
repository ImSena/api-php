<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Media;
use App\Model\Order;
use App\Model\Product;
use App\Utils\Pagination;
use App\Utils\Validator;
use Exception;
use PDO;
use PDOException;

class OrderService
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(array $data)
    {
        try {

            $Order = new Order($this->pdo);

            $fields = Validator::validate([
                "id_address" => $data['id_address'] ?? '',
                "order_items" => $data['order_items'] ?? ''
            ]);
            $fields['id_user'] = $data['id_user'];
            $fields['id_coupon'] = $data['id_coupon'] ?? null;

            $Order = $Order->create($fields);

            if (!$Order) {
                throw new Exception("Não foi possível realizar pedidos");
            }

            return [
                'message' => "Pedido realizado com sucesso",
                'id_order' => $Order
            ];
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    public function getAll(array $data)
    {
        try {

            $Order = new Order($this->pdo);
            $Product = new Product($this->pdo);
            $Media = new Media($this->pdo);
            $AddressService = new AddressService($this->pdo);
            $UserService = new UserService($this->pdo);
            $MediaService = new MediaService($this->pdo);
            $statusOrder = [
                'PENDING',
                'PROCESSING',
                'SHIPPED',
                'DELIVERED',
                'CANCELLED',
                'REFUNDED',
                'RETURNED',
                'ALL'
            ];

            $limitPage = 0;

            $fields = Validator::validatePermission([
                "id_user" => $data['id_user'] ?? '',
                "rule" => $data['rule'] ?? ''
            ]);

            $limitPage = $data['rule'] == 'admin' ? 25 : 10;
            $fields['params'] = $data['params'];

            if (count($fields['params']) > 1) {
                $fields['params']['status'] = strtoupper($fields['params']['status']);
                if (!in_array($fields['params']['status'], $statusOrder)) {
                    throw new Exception("Status do pedido inexistente");
                }

                if ($fields['params']['status'] == 'ALL') {
                    $OrderResult = $Order->getAll($fields);
                    $totalOrders = $Order->getTotalOrders($fields);
                } else {
                    $OrderResult = $Order->getAllStatus($fields);
                    $totalOrders = $Order->getTotalStatus($fields);
                }
            }

            if (!$OrderResult) {
                throw new Exception("Não foi possível buscar pedidos");
            }

            if (!$totalOrders) {
                throw new Exception("Não foi possível buscar quantidade total de pedidos");
            }

            foreach ($OrderResult as &$item) {
                $productItem = $Order->getOrderIdProductItems($item['id_order']);

                $address = $AddressService->getById($item['id_address']);
                unset($address['content']['id_user']);
                $item['address_shipped'] = $address['content'];
                unset($item['id_address']);

                foreach ($productItem as $product) {
                    $productData = $Product->getById($product['id_product_variant']);
                    $path = $Media->getPathToFile($productData);
                    $extension = $MediaService->getExtension($productData['file_type']);
                    $productData['image_path'] = $path . '.' . $extension;
                    $productData['quantity'] = $product['quantity'];
                    unset($productData['id_media']);
                    unset($productData['file_type']);
                    unset($productData['qtd_stock']);
                    $item['products'][] = $productData;
                }

                if ($data['rule'] == 'admin') {
                    $User = $UserService->getById(1);
                    $item['user'] = $User['content'];
                }

                unset($item['id_user']);
            }

            $qtdPage = Pagination::calculateTotalPages($totalOrders['total'],  $limitPage);

            $pages = [
                "qtdPage" => $qtdPage,
                "total" => $totalOrders['total']
            ];

            return [
                'message' => 'Pedidos encontrados com sucesso',
                'content' => $OrderResult,
                "page" => $pages,
            ];
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getAllOrder(array $data)
    {
        try {

            $Order = new Order($this->pdo);
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getById(int $id)
    {
        try {
            $Order = new Order($this->pdo);
            $Media = new Media($this->pdo);
            $OrderResult = $Order->getById($id);
            $Product = new Product($this->pdo);
            $MediaService = new MediaService($this->pdo);

            if (!$OrderResult) {
                throw new Exception("Não foi possível buscar pedido");
            }

            $productItems = $Order->getOrderIdProductItems($id);

            $totalPrice = 0.00;
            foreach ($productItems as $productItem) {
                $product = $Product->getById($productItem['id_product_variant']);
                $path = $Media->getPathToFile($product);
                $extension = $MediaService->getExtension($product['file_type']);
                $product['image_path'] = $path.'.'.$extension;
                unset($product['qtd_stock']);
                unset($product['id_media']);
                $OrderResult['products'][] = $product;
                $price = floatval($product['price']);
                $discount = floatval($product['discount']);
                $price -= $discount;
                $totalPrice += $price * intval($productItem['quantity']);
            }

            $OrderResult['total_price'] = number_format($totalPrice, 2, ',', '.');

            return [
                'message' => 'Pedido encontrado com sucesso',
                'content' => $OrderResult
            ];
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    public function changeStatus(string $status, int $id_order)
    {
        try {

            $statusExisting = [
                'PENDING',
                'PROCESSING',
                'SHIPPED',
                'DELIVERED',
                'CANCELLED',
                'REFUNDED',
                'RETURNED'
            ];

            if (!in_array($status, $statusExisting)) {
                throw new Exception("Status inválido: $status");
            }

            $Order = new Order($this->pdo);

            $result = $Order->changeStatus($status, $id_order);

            if (!$result) {
                throw new Exception("Não foi possível alterar o status");
            }

            return "Status do pedido alterado com sucesso.";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    public function verifyOrder(int $id)
    {
        try {

            $Order = new Order($this->pdo);

            $result = $Order->verifyStatus($id);

            if (!$result) {
                throw new Exception("Não foi possível encontrar nenhum status para esse pedido");
            }

            return $result;
        } catch (PDOException $e) {
            return [
                'error' => DatabaseErrorHelpers::error($e)
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    public function insertPayment(array $data)
    {
        try{

            $fields = Validator::validate([
                "id" => $data['id'],
                "payment_url" => $data['payment_url'],
                "payment_expires_at" => $data['payment_expires_at']
            ]);

            $Order = new Order($this->pdo);

            if(!$Order->insertPayment($fields)){
                throw new Exception("Não foi possível inserir link de pagamento");
            }

            return "Pagamento inserido com sucesso";

        }catch(PDOException $e){
            return ['error' => DatabaseErrorHelpers::error($e)];
        }
        catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    public function cancellPayment(int $id)
    {
        try{
            $this->pdo->beginTransaction();

            $Order = new Order($this->pdo);
            $ProductService = new ProductService($this->pdo);

            $items = $Order->getOrderIdProductItems($id);

            foreach($items as $item){
                $data = [
                    "id" => $item['id_product_variant'],
                    "quantity" => $item['quantity']
                ];

                $result = $ProductService->insertQuantity($data);

                if(isset($result['error'])){
                    throw new Exception("Não foi possível cancelar pedido.");
                }
            }

            $result = $this->changeStatus("CANCELLED", $id);

            if(isset($result['error'])){
                throw new Exception("Não foi possível cancelar pedido.");
            }

            $this->pdo->commit();

        }catch(PDOException $e){
            $this->pdo->rollBack();
            return [
                'error' => DatabaseErrorHelpers::error($e)
            ];
        }catch(Exception $e){
            $this->pdo->rollBack();
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}
