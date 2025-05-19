<?php

namespace App\Service;

use App\Exceptions\EmailNotSentException;
use App\Exceptions\RouteNotFoundException;
use App\Helpers\DatabaseErrorHelpers;
use App\Helpers\OrderNotificationFormatter;
use App\Service\Base\BaseService;
use App\Stripe\Keys;
use Exception;
use PDOException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
use UnexpectedValueException;

class WebhookService extends BaseService
{
    public function processEvent($payload, $headers)
    {
        try {
            $this->pdo->beginTransaction();
            date_default_timezone_set("America/Sao_Paulo");

            Stripe::setApiKey(Keys::getSecretKey());

            $endpoint_secret = 'whsec_3fcbf7f08712998faf75112d825625123e7f09fa427ff879db49847baef15aa4';

            $event = null;

            $response = "tudo certo";

            if (isset($headers['stripe-signature'])) {
                $sig_header = $headers['stripe-signature'];

                $event = Webhook::constructEvent(
                    $payload,
                    $sig_header,
                    $endpoint_secret
                );
            } else if (isset($headers['x-from-system']) && $headers['x-from-system'] === 'true') {
                if (is_string($payload)) {
                    $event = json_decode($payload);
                } else {
                    $event = (object) $payload;
                }
            } else {
                throw new RouteNotFoundException("Não foi possível encontrar evento");
            }

            switch ($event->event_type ?? $event->type) {
                case 'payment_intent.succeeded':

                    $Notification = new NotificationsService($this->pdo);
                    $Order = new OrderService($this->pdo);
                    $Payment = new PaymentService($this->pdo);
                    $AddressService = new AddressService($this->pdo);

                    $data = $event->data;
                    $id_order = $data['metadata']['id_order'];
                    $email = $data['metadata']['email'];
                    $id_transaction = $data['id'];
                    $status = 'PAID';
                    $payment_method = $data['payment_method_types'][0] ?? 'unknown';
                    switch ($payment_method) {
                        case "card":
                            $payment_method = 'CARD';
                            break;
                        case "boleto":
                            $payment_method = 'BANK_SPLIP';
                            break;
                        case "pix":
                            $payment_method = 'PIX';
                            break;
                        default:
                            $payment_method = "N";
                    }
                    $amount = floatval($data['amount'] / 100);
                    $payment_date = date("Y-m-d H:i:s", $data['created']);

                    $orderResult = $Order->getById($id_order);

                    if (isset($orderResult['error'])) {
                        throw new Exception("Não foi possivel resgatar dados do pedido. Id_pedido:$id_order, Status do pedido: Pago, Email: $email, Id_transação: $id_transaction");
                    }

                    $orderResult = $orderResult['content'];

                    $address = $AddressService->getById($orderResult['id_user']);

                    if (isset($address['error'])) {
                        throw new Exception("Não foi possível resgatar endereço do usuário. Id_pedido:$id_order, Status do pedido: Pago, Email: $email, Id_transação: $id_transaction");
                    }

                    $address = $address['content'];

                    $orderData = OrderNotificationFormatter::format($orderResult, $email, $address);
                    $orderData['id'] = $id_order;
                    try {
                        $sendEmail = $Notification->notifyOrderPaySuccess($orderData);

                        if (isset($sendEmail['error'])) {
                            throw new EmailNotSentException("Não foi possível enviar email de requisição");
                        }
                    } catch (EmailNotSentException $e) {
                        $error = new ErrorService($this->pdo);

                        $error->register([
                            'source' => 'email_notification',
                            'context' => json_encode([
                                'id_order' => $id_order,
                                'email' => $email,
                                "status_pedido" => "pago"
                            ]),
                            'message' => $e->getMessage(),
                            'payload' => json_encode($orderData)
                        ]);

                        $sendEmail = false;
                    }

                    $dataService = [
                        'id_order' => $id_order,
                        'id_transaction' => $id_transaction,
                        'body_transaction' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'status' => $status,
                        'amount' => $amount,
                        'payment_method' => $payment_method,
                        'payment_date' => $payment_date,
                        'send_email' => $sendEmail
                    ];

                    $orderService = $Order->changeStatus('PROCESSING', $id_order);

                    if (isset($orderService['error'])) {
                        throw new Exception($orderService['error'].". Id_pedido:$id_order, Status do pedido: Pago, Email: $email, Id_transação: $id_transaction");
                    }

                    $paymentRegistered = $Payment->register($dataService);

                    if (isset($paymentRegistered['error'])) {
                        throw new Exception($paymentRegistered['error'].". Id_pedido:$id_order, Status do pedido: Pago, Email: $email, Id_transação: $id_transaction");
                    }

                    $response =  'Pagamento confirmado';

                    break;
                case 'payment_intent.payment_failed':
                    $Notification = new NotificationsService($this->pdo);
                    $Order = new OrderService($this->pdo);
                    $Payment = new PaymentService($this->pdo);
                    $AddressService = new AddressService($this->pdo);

                    $data = $event->data;
                    $id_order = $data['metadata']['id_order'];
                    $email = $data['metadata']['email'];
                    $id_transaction = $data['id'];
                    $status = 'DECLINED';
                    $payment_method = $data['payment_method_types'][0] ?? 'unknown';
                    switch ($payment_method) {
                        case "card":
                            $payment_method = 'CARD';
                            break;
                        case "boleto":
                            $payment_method = 'BANK_SPLIP';
                            break;
                        case "pix":
                            $payment_method = 'PIX';
                            break;
                        default:
                            $payment_method = "N";
                    }
                    $amount = floatval($data['amount'] / 100);
                    $payment_date = date("Y-m-d H:i:s", $data['created']);

                    $orderResult = $Order->getById($id_order);

                    if (isset($orderResult['error'])) {
                        throw new Exception("Não foi possivel resgatar dados do pedido. Id_pedido:$id_order, Status do pedido: Recusado, Email: $email, Id_transação: $id_transaction");
                    }

                    $orderResult = $orderResult['content'];

                    $address = $AddressService->getById($orderResult['id_user']);

                    if (isset($address['error'])) {
                        throw new Exception("Não foi possível resgatar endereço do usuário. Id_pedido:$id_order, Status do pedido: Pago, Email: $email, Id_transação: $id_transaction");
                    }

                    $address = $address['content'];

                    $orderData = OrderNotificationFormatter::format($orderResult, $email, $address);
                    $orderData['id'] = $id_order;

                    $orderService = $orderService = $Order->cancellPayment($id_order);

                    if (isset($orderService['error'])) {
                        throw new Exception($orderService['error'].". Id_pedido:$id_order, Status do pedido: Pago, Email: $email, Id_transação: $id_transaction");
                    }

                    try {
                        $sendEmail = $Notification->notifyOrderPayFailed($orderData);

                        if (isset($sendEmail['error'])) {
                            throw new EmailNotSentException("Não foi possível enviar email de requisição.");
                        }
                    } catch (EmailNotSentException $e) {
                        $error = new ErrorService($this->pdo);

                        $error->register([
                            'source' => 'email_notification',
                            'context' => json_encode([
                                'id_order' => $id_order,
                                'email' => $email
                            ]),
                            'message' => $e->getMessage(),
                            'payload' => json_encode($orderData)
                        ]);

                        $sendEmail = false;
                    }

                    $dataService = [
                        'id_order' => $id_order,
                        'id_transaction' => $id_transaction,
                        'body_transaction' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'status' => $status,
                        'amount' => $amount,
                        'payment_method' => $payment_method,
                        'payment_date' => $payment_date,
                        'send_email' => $sendEmail
                    ];

                    $paymentRegistered = $Payment->register($dataService);

                    if (isset($paymentRegistered['error'])) {
                        throw new Exception($paymentRegistered['error'].". Id_pedido:$id_order, Status do pedido: Pago, Email: $email, Id_transação: $id_transaction");
                    }
                    $response = 'Pagamento falhou ou expirou';
                default:
                    throw new RouteNotFoundException("Não foi possível encontrar evento. Requisição inválida");
            }

            $this->pdo->commit();

            return $response;
        } catch (SignatureVerificationException $e) {
            $this->pdo->rollBack();
            return [
                'error' => $e->getMessage()
            ];
        } catch (UnexpectedValueException $e) {
             $this->pdo->rollBack();
            return [
                'error' => $e->getMessage()
            ];
        } catch (PDOException $e) {
             $this->pdo->rollBack();
            return [
                'error' => DatabaseErrorHelpers::error($e)
            ];
        } catch (Exception $e) {
             $this->pdo->rollBack();
            return [
                'error' => $e->getMessage()
            ];
        } catch (RouteNotFoundException $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}
