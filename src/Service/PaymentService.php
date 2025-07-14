<?php

namespace App\Service;

use App\Model\Order;
use App\Model\Payment;
use App\Model\Store;
use App\Service\Base\BaseService;
use App\Stripe\Keys;
use App\Utils\Validator;
use DateTime;
use DateTimeZone;
use Exception;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Stripe;

require_once __DIR__ . '/../../config.php';

class PaymentService extends BaseService
{
    public function payOrder(array $data)
    {
        return $this->execute(function () use ($data) {
            $Order = new OrderService($this->pdo);
            $UserService = new UserService($this->pdo);
            $addressService = new AddressService($this->pdo);
            $shippingService = new OrderShippingService($this->pdo);

            $status = $Order->verifyOrder($data['id_order']);

            if (isset($status['error'])) {
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
                $shippingResult = $shippingService->getOrderShipping($data['id_order']);

                $fields['id_order'] = $data['id_order'];
                $orderService = $Order->getById($fields['id_order']);
                $orderService = $orderService['content'];

                $created_at = new DateTime($orderService['created_at'], new DateTimeZone('America/Sao_Paulo'));
                $now = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
                $interval = $created_at->diff($now);

                if ($interval->i + ($interval->h * 60) + ($interval->d * 1440) > 15) {
                    $result = $Order->cancellPayment($fields['id_order']);
                    if (isset($result['error'])) {
                        throw new Exception($result['error']);
                    }

                    throw new Exception("Pedido expirado. Por favor, gere um novo pedido.");
                }

                $user = $UserService->getById($orderService['id_user']);
                $user = $user['content'];

                $phone = $user['contact'][0]['number'];

                $address = $addressService->getById($orderService['id_address']);
                $address = $address['content'];

                $storeModel = new Store($this->pdo);
                $store = $storeModel->getActiveStore();

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


                $shippingCost = floatval($shippingResult['shipping_cost']);
                $rawTotal = $orderService['total_price'];

                if (is_string($rawTotal)) {
                    $rawTotal = str_replace('.', '', $rawTotal);
                    $rawTotal = str_replace(',', '.', $rawTotal);
                }

                $totalPrice = floatval($rawTotal) + $shippingCost;

                $sessionParams = [
                    //para add boleto basta colocar ,boleto
                    "payment_method_types" => ['card'],
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
                            'domain' => URL_API,
                            'email' => $user['email'],
                            'stripe_account_id' => $store['stripe_account_id'],
                        ]
                    ],
                    'metadata' => [
                        'id_order' => $data['id_order'],
                        'domain' => URL_API,
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
                    "payment_expires_at" => date("Y-m-d H:i:s", $expiresAt)
                ];

                $result = $Order->insertPayment($dataPayment);

                if (isset($result['error'])) {
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

                if ($expiration < $now) {
                    $result = $Order->cancellPayment($data['id_order']);
                    if (isset($result['error'])) {
                        throw new Exception("Ocorreu um erro inesperado. Por favor, tente mais tarde.");
                    }

                    throw new Exception("Pedido expirado. Por favor, gere um novo pedido.");
                }

                $result = $this->getPayment($data['id_order']);

                if (isset($result['error'])) {
                    throw new Exception("Não foi possível recuperar link de pagamento.");
                }

                return [
                    'session_url' => $result['payment_url'],
                    'message' => "Pagamento recuperado com sucesso."
                ];
            }
        });
    }

    public function getPayment(int $id)
    {
        return $this->execute(function () use ($id) {
            $Order = new Order($this->pdo);

            $result = $Order->getLinkPayment($id);

            if (!$result) {
                throw new Exception("Não foi possível recuperar link de pagamento!");
            }

            return $result;
        });
    }

    private function getTime(): int
    {
        $now = time();

        return $now;
    }

    public function register(array $data)
    {
        return $this->execute(function () use ($data) {
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
        });
    }

    public function getReport()
    {
        return $this->execute(function () {
            $Payment = new Payment($this->pdo);
            $result = $Payment->getReport();

            if (!$result) {
                throw new Exception("Não foi possível resgatar dados de pagamento");
            }

            $total = array_sum(array_column($result, 'monthly_total'));

            $yearly = [];

            foreach ($result as $row) {
                $year = substr($row['month'], 0, 4);
                $yearly[$year] = ($yearly[$year] ?? 0) + floatval($row['monthly_total']);
            }

            $monthly = $this->getFormattedMonthlyBilling($result);

            return [
                'total' => $total,
                'monthly' => $monthly,
                'yearly' => $yearly
            ];
        });
    }
    private function getFormattedMonthlyBilling(array $rawData): array
    {
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        $formatted = [];

        foreach ($rawData as $item) {
            $year = date('Y', strtotime($item['month']));
            $monthIndex = (int)date('m', strtotime($item['month'])) - 1;
            $monthKey = $months[$monthIndex];

            if (!isset($formatted[$year])) {
                foreach ($months as $m) {
                    $formatted[$year][$m] = ['total' => '0.00'];
                }
            }

            $formatted[$year][$monthKey]['total'] = number_format($item['monthly_total'], 2, '.', '');
        }

        return $formatted;
    }
}
