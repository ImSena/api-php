<?php

namespace App\Service;

use App\Helpers\OrderNotificationFormatter;
use App\Model\Media;
use App\Model\Order;
use App\Model\Product;
use App\Model\ProductCategory;
use App\Service\Base\BaseService;
use App\Utils\Pagination;
use App\Utils\Validator;
use Exception;

require_once __DIR__ . '/../../config.php';

class OrderService extends BaseService
{
    public function create(array $data)
    {
        return $this->execute(function () use ($data) {
            $orderModel = new Order($this->pdo);
            $OrderShippingService = new OrderShippingService($this->pdo);
            $UserService = new UserService($this->pdo);
            $AddressService = new AddressService($this->pdo);
            $NotificationService = new NotificationsService($this->pdo);

            $fields = Validator::validate([
                "id_address" => $data['id_address'] ?? '',
                "order_items" => $data['order_items'] ?? '',
                "shipping_signature" => $data['shipping_signature'] ?? ''
            ]);

            $fields['id_user'] = $data['id_user'];
            $fields['id_coupon'] = $data['id_coupon'] ?? null;

            $orderId = $orderModel->create($fields);

            if (!$orderId) {
                throw new Exception("Não foi possível realizar pedidos");
            }

            $dataShipping = [
                "shipping_signature" => $fields['shipping_signature'],
                "id_order" => $orderId
            ];
            $shippingSignature = $OrderShippingService->createShipping($dataShipping);

            if (isset($shippingSignature['error'])) {
                throw new Exception("Assinatura de cotação inválida");
            }

            $orderResponse = $this->getById($orderId);
            if (isset($orderResponse['error'])) {
                throw new Exception("Erro ao buscar pedido");
            }
            $order = $orderResponse['content'];
            $order['id'] = $orderId;

            $resultUser = $UserService->getById($order['id_user']);

            if (isset($resultUser['error'])) {
                throw new Exception("Não foi possível carregar usuário");
            }

            $user = $resultUser['content'];

            $resultAddress = $AddressService->getById($order['id_address']);

            if (isset($resultAddress['error'])) {
                throw new Exception("Não foi possível carregar endereço");
            }

            $address = $resultAddress['content'];

            $dataOrder = OrderNotificationFormatter::format($order, $user['email'], $address);
            $dataOrder['id'] = $orderId;

            $send = $NotificationService->notifyOrderCreated($dataOrder);

            if (isset($send['error'])) {
                throw new Exception("Não foi possível enviar notificação.");
            }

            return [
                'message' => "Pedido realizado com sucesso",
                'id_order' => $orderId,
            ];
        }, true);
    }

    public function getAll(array $data)
    {
        return $this->execute(function () use ($data) {
            $Order = new Order($this->pdo);
            $Product = new Product($this->pdo);
            $Media = new Media($this->pdo);
            $AddressService = new AddressService($this->pdo);
            $UserService = new UserService($this->pdo);
            $OrderShippingService = new OrderShippingService($this->pdo);
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
                $orderShipping = $OrderShippingService->getOrderShipping($item['id_order']);

                // if (isset($orderShipping['error'])) {
                //     throw new Exception($orderShipping['error']);
                // }
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
                    $productData['shipping'] = $orderShipping;
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
        });
    }

    public function getById(int $id)
    {
        return $this->execute(function () use ($id) {
            $Order = new Order($this->pdo);
            $Media = new Media($this->pdo);
            $OrderResult = $Order->getById($id);
            $Product = new Product($this->pdo);
            $MediaService = new MediaService($this->pdo);
            $OrderShippingService = new OrderShippingService($this->pdo);
            $ProductCategories = new ProductCategory($this->pdo);

            if (!$OrderResult) {
                throw new Exception("Não foi possível buscar pedido");
            }
            $OrderShippingService = $OrderShippingService->getOrderShipping($id);

            if (isset($OrderShippingService['error'])) {
                throw new Exception("Não foi possível enviar buscar dados de frete do produto");
            }

            $productItems = $Order->getOrderIdProductItems($id);

            $totalPrice = 0.00;
            foreach ($productItems as $productItem) {
                $product = $Product->getById($productItem['id_product_variant']);
                $product['category'] = $ProductCategories->getCategoryVariant($productItem['id_product_variant'])['category_name'];
                $path = $Media->getPathToFile($product);
                $extension = $MediaService->getExtension($product['file_type']);
                $product['image_path'] = $path . '.' . $extension;
                $product['quantity'] = $productItem['quantity'];
                unset($product['qtd_stock']);
                unset($product['id_media']);
                $OrderResult['products'][] = $product;
                $price = floatval($product['price']);
                $discount = floatval($product['discount']);
                $price -= $discount;
                $totalPrice += $price * intval($productItem['quantity']);
            }

            $OrderResult['shipping'] = [
                "carrier" => $OrderShippingService['carrier'],
                "shipping_type" => $OrderShippingService['shipping_type'],
                "shipping_cost" => $OrderShippingService['shipping_cost'],
            ];

            $OrderResult['total_price'] = number_format($totalPrice, 2, ',', '.');


            return [
                'message' => 'Pedido encontrado com sucesso',
                'content' => $OrderResult
            ];
        });
    }
    public function changeStatus(string $status, int $id_order)
    {
        return $this->execute(function () use ($status, $id_order) {
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
        });
    }
    public function verifyOrder(int $id)
    {
        return $this->execute(function () use ($id) {
            $Order = new Order($this->pdo);

            $result = $Order->verifyStatus($id);

            if (!$result) {
                throw new Exception("Não foi possível encontrar nenhum status para esse pedido");
            }

            return $result;
        });
    }

    public function insertPayment(array $data)
    {
        return $this->execute(function () use ($data) {
            $fields = Validator::validate([
                "id" => $data['id'],
                "payment_url" => $data['payment_url'],
                "payment_expires_at" => $data['payment_expires_at']
            ]);

            $Order = new Order($this->pdo);

            if (!$Order->insertPayment($fields)) {
                throw new Exception("Não foi possível inserir link de pagamento");
            }

            return "Pagamento inserido com sucesso";
        });
    }

    public function cancellPayment(int $id)
    {
        return $this->execute(function () use ($id) {
            $Order = new Order($this->pdo);
            $ProductService = new ProductService($this->pdo);

            $items = $Order->getOrderIdProductItems($id);

            foreach ($items as $item) {
                $data = [
                    "id" => $item['id_product_variant'],
                    "quantity" => $item['quantity']
                ];

                $result = $ProductService->insertQuantity($data);

                if (isset($result['error'])) {
                    throw new Exception("Não foi possível cancelar pedido.");
                }
            }

            $result = $this->changeStatus("CANCELLED", $id);

            if (isset($result['error'])) {
                throw new Exception("Não foi possível cancelar pedido.");
            }
        }, true);
    }
}
