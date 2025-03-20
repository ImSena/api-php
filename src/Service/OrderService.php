<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Order;
use App\Model\Product;
use App\Utils\Pagination;
use App\Utils\Validator;
use Exception;
use PDOException;

class OrderService
{
    public static function create(array $data)
    {
        try {

            $fields = Validator::validate([
                "id_address" => $data['id_address'] ?? '',
                "order_items" => $data['order_items'] ?? ''
            ]);
            $fields['id_user'] = $data['id_user'];
            $fields['id_coupon'] = $data['id_coupon'] ?? null;

            $Order = Order::create($fields);

            if (!$Order) {
                throw new Exception("Não foi possível realizar pedidos");
            }

            return "Pedido realizado com sucesso";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function getAll(array $data)
    {
        try {
            $statusOrder = [
                'PENDING',
                'PROCESSING',
                'SHIPPED',
                'DELIVERED',
                'CANCELLED',
                'REFUNDED',
                'RETURNED'
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

                $Order = Order::getAllStatus($fields);
                $totalOrders = Order::getTotalStatus($fields);
            } else {
                $Order = Order::getAll($fields);
                $totalOrders = Order::getTotalOrders($fields);
            }

            if (!$Order) {
                throw new Exception("Não foi possível buscar pedidos");
            }

            if (!$totalOrders) {
                throw new Exception("Não foi possível buscar quantidade total de pedidos");
            }

            foreach($Order as &$item){
                $productItem = Order::getOrderIdProductItems($item['id_order']);
                
                $address = AddressService::getById($item['id_address']);
                unset($address['content']['id_user']);
                $item['address_shipped'] = $address['content']; 
                unset($item['id_address']);

                foreach($productItem as $product){
                    $item['products'][] = Product::getById($product['id_product_variant']);
                }   
                
                if($data['rule'] == 'admin'){
                    $User = UserService::getById(1);
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
                'content' => $Order,
                "page" => $pages,
            ];
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function getById(int $id)
    {
        try {
            $Order = Order::getById($id);

            if (!$Order) {
                throw new Exception("Não foi possível buscar pedido");
            }

            return [
                'message' => 'Pedido encontrado com sucesso',
                'content' => $Order
            ];
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
