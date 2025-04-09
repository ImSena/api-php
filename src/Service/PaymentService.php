<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Payment;
use App\Model\Stripe\Store;
use App\Stripe\Keys;
use App\Utils\Validator;
use Exception;
use PDOException;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Stripe;

require_once __DIR__ . '/../../config.php';

class PaymentService
{
    public static function payOrder(array $data)
    {
        try{

            $status = OrderService::verifyOrder($data['id_order']);

            $status = $status['status'];

            if($status !== 'PENDING'){
                throw new Exception("Esse pedido já foi pago!");
            }

            Stripe::setApiKey(Keys::getSecretKey());

            $fields['id_order'] = $data['id_order'];
            $orderService = OrderService::getById($fields['id_order']);
            $user = UserService::getById($orderService['content']['id_user']);
            $user = $user['content'];
            $phone = $user['contact'][0]['number'];
            $address = AddressService::getById($orderService['content']['id_address']);
            $address = $address['content'];

            $storeModel = new Store();
            $store = $storeModel->findById(1);

            $options = [
                'stripe_account' => $store['stripe_account_id']
            ];

            if (!$store) {
                throw new Exception("Lojista não encontrado.");
            }
            
            if (empty($store['stripe_account_id'])) {
                throw new Exception("Conta do Stripe não vinculada");
            }
         
            $customer = Customer::create([
                'email' => $user['email'],
                'name' => $user['username'],
                'phone' => "+55".$phone,
                'address' => [
                    'line1' => $address['public_area'],
                    'line2' => !empty($address['complement']) ? $address['complement'] : null,
                    'city' => $address['city'],
                    'state' => $address['state'],
                    'postal_code' => $address['zip_code'],
                    'country' => 'BR'
                ]
            ], $options);

            $totalPrice = $orderService['content']['total_price'];
            $totalPrice = str_replace(['.', ','], ['', '.'], $totalPrice);
            $totalPrice = floatval($totalPrice);

            $sessionParams = [
                "payment_method_types" => ['card', 'boleto'],
                "line_items" => [
                    [
                        'price_data' => [
                            'currency' => 'brl',
                            'product_data' => [
                                'name' => "#Pedido n°".$data['id_order'],
                            ],
                            'unit_amount' => intval($totalPrice * 100),
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => SUCCESS_URL,
                'cancel_url' => CANCEL_URL,
                'customer' => $customer->id,
                'payment_intent_data' => [
                    'metadata' => [
                        'id_order' => $data['id_order'],
                        'domain' => 'http://localhost/api-php/',
                        'email' => $user['email'],
                        'stripe_account_id' => $store['stripe_account_id'],
                    ]
                ],
                'metadata' => [
                    'id_order' => $data['id_order'],
                    'domain' => 'http://localhost/api-php/',
                    'email' => $user['email'],
                    'stripe_account_id' => $store['stripe_account_id'],
                ]
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

    public static function register(array $data)
    {
        try{

            $fields = Validator::validate([
                "id_order" => $data['id_order'] ?? '',
                "id_transaction" => $data['id_transaction'] ?? '',
                "body_transaction" => $data['body_transaction'] ?? '',
                "status" => $data['status'] ?? '',
                'amount' => $data['amount'] ?? '',
                "payment_method" => $data['payment_method'] ?? '',
                'payment_date' => $data['payment_date'] ?? '',
                'send_email' => $data['send_email']
            ]);

            $Payment = new Payment();
            $Payment->register($fields);

            if(!$Payment){
                throw new Exception("Não foi possível cadastrar registro do pagamento");
            }

            return "Produto cadastrado com sucesso!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
