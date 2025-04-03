<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Stripe\Store;
use App\Stripe\Keys;
use App\Utils\Validator;
use Exception;
use PDOException;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PaymentService
{
    public static function payOrder(array $data)
    {
        try {

            Stripe::setApiKey(Keys::getSecretKey());

            $storeModel = new Store();
            $store = $storeModel->findById(1);

            if (!$store) {
                throw new Exception("Lojista não encontrado.");
            }

            if (empty($store['stripe_account_id'])) {
                throw new Exception("Conta do Stripe não vinculada");
            }

            $fields['id_order'] = $data['id_order'];

            $orderService = OrderService::getById($fields['id_order']);


            $totalPrice = $orderService['content']['total_price'];
            $totalPrice = floatval($totalPrice);

            $sessionParams = [
                "payment_method_types" => ['card', 'boleto'],
                "line_items" => [
                    [
                        'price_data' => [
                            'currency' => 'brl',
                            'product_data' => [
                                'name' => "#Pedido n°1",
                            ],
                            'unit_amount' => intval($totalPrice * 100),
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => 'http://escalaweb.com.br',
                'cancel_url' => 'http://escalaweb.com.br/sac',
            ];

            $options = [
                'stripe_account' => $store['stripe_account_id']
            ];

            $session = Session::create($sessionParams, $options);

            return [
                'session_url' => $session->url,
                'message' => "Pagamento gerado com sucesso"
            ];
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
