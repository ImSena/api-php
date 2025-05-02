<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Order;
use App\Model\Payment;
use App\Model\Store;
use App\Stripe\Keys;
use App\Utils\Validator;
use DateTime;
use DateTimeZone;
use Exception;
use PDO;
use PDOException;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Stripe;

require_once __DIR__ . '/../../config.php';

class PaymentService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function payOrder(array $data)
    {
        try {
            $Order = new OrderService($this->pdo);
            $UserService = new UserService($this->pdo);
            $addressService = new AddressService($this->pdo);

            $status = $Order->verifyOrder($data['id_order']);

            if(isset($status['error'])){
                throw new Exception("Não foi possível encontrar pedido");
            }

            $status = $status['status'];

            if ($status !== 'PENDING' || $status == 'CANCELLED' || $status == 'REFUNDED' || $status == 'RETURNED') {
                throw new Exception("Esse pedido não pode ser pago!");
            }

            $payment = $Order->getById($data['id_order']);

            $payment_url = $payment['content']['payment_url'];

            if (!$payment_url) {
                Stripe::setApiKey(Keys::getSecretKey());

                $fields['id_order'] = $data['id_order'];
                $orderService = $Order->getById($fields['id_order']);
                $user = $UserService->getById($orderService['content']['id_user']);
                $user = $user['content'];
                $phone = $user['contact'][0]['number'];
                $address = $addressService->getById($orderService['content']['id_address']);
                $address = $address['content'];

                $storeModel = new Store($this->pdo);
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
                    'phone' => "+55" . $phone,
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
                                    'name' => "#Pedido n°" . $data['id_order'],
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

                $time = $this->getTime();
                $expiresAt = $time + 3600;

                $dataPayment = [
                    "id" => $data['id_order'],
                    "payment_url" => $session->url,
                    "payment_expires_at" => date("Y-m-d H:i:s",$expiresAt)
                ];

                $result = $Order->insertPayment($dataPayment);

                if(isset($result['error'])){
                    throw new Exception("Não foi possível gerar pagamento");
                }

                return [
                    'session_url' => $session->url,
                    'message' => "Pagamento recuperado com sucesso"
                ];
            } else {
                $payment_expired = $payment['content']['payment_expires_at'];
                $now = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
                $expiration = DateTime::createFromFormat('Y-m-d H:i:s', $payment_expired, new DateTimeZone('America/Sao_Paulo'));

                if($expiration < $now)
                {
                    $result = $Order->cancellPayment($data['id_order']);
                    if(isset($result['error'])){
                        throw new Exception("Ocorreu um erro inesperado. Por favor, tente mais tarde.");
                    }

                    throw new Exception("Pagamento expirado. Por favor, gere um novo pedido.");
                }

                $result = $this->getPayment($data['id_order']);

                if(isset($result['error'])){
                    throw new Exception("Não foi possível recuperar link de pagamento.");
                }

                return [
                    'session_url' => $result['payment_url'],
                    'message' => "Pagamento recuperado com sucesso."
                ];
            }
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getPayment(int $id)
    {
        try{

            $Order = new Order($this->pdo);

            $result = $Order->getLinkPayment($id);

            if(!$result){
                throw new Exception("Não foi possível recuperar link de pagamento!");
            }

            return $result;

        }catch(PDOException $e){
            return ['error' => DatabaseErrorHelpers::error($e)];
        }catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    private function getTime():int
    {
        $now = time();

        return $now;
    }

    public function register(array $data)
    {
        try {
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

            $Payment = new Payment($this->pdo);
            $Payment->register($fields);

            if (!$Payment) {
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
