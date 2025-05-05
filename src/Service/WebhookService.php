<?php

namespace App\Service;

use App\Exceptions\RouteNotFoundException;
use App\Helpers\DatabaseErrorHelpers;
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
            }else if(isset($headers['x-from-system']) && $headers['x-from-system'] === 'true'){
                if (is_string($payload)) {
                    $event = json_decode($payload);
                } else {
                    $event = (object) $payload;
                }
            }else {
                throw new RouteNotFoundException();
            }

            switch($event->event_type ?? $event->type){
                case 'payment_intent.succeeded':

                    $Notification = new NotificationsService($this->pdo);
                    $Order = new OrderService($this->pdo);
                    $Payment = new PaymentService($this->pdo);

                    $data = $event->data;
                    $id_order = $data['metadata']['id_order'];
                    $email = $data['metadata']['email'];
                    $id_transaction = $data['id'];
                    $status = 'PAID';
                    $payment_method = $data['payment_method_types'][0];
                    switch($payment_method){
                        case "card": $payment_method = 'CARD'; 
                        break;
                        case "boleto" : $payment_method = 'BANK_SPLIP';
                        break;
                        case "pix": $payment_method = 'PIX';
                        break;
                        default: $payment_method = "N";
                    }
                    $amount = floatval($data['amount'] / 100);
                    $payment_date = date("Y-m-d H:i:s",$data['created']);

                    $notificationService = $Notification->sendNotificationsClient('PAYMENT_SUCCESS', $email);
                    $notificationAdminService = $Notification->sendNotificationsAdmin('ORDER_PROCESSING', []);

                    $send_email = isset($notificationService['error']) || isset($notificationAdminService['error']) ? false : true;

                    $dataService = [
                        'id_order' => $id_order,
                        'id_transaction' => $id_transaction,
                        'body_transaction' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'status' => $status,
                        'amount' => $amount,
                        'payment_method' => $payment_method,
                        'payment_date' => $payment_date,
                        'send_email' => $send_email
                    ];

                    $orderService = $Order->changeStatus('PROCESSING', $id_order);

                    if(isset($orderService['error'])){
                        throw new Exception($orderService['error']);
                    }

                    $paymentRegistered = $Payment->register($dataService);

                    if(isset($paymentRegistered['error'])){
                        throw new Exception($paymentRegistered['error']);
                    }

                    $response =  'Pagamento confirmado';

                break;
                default:

            }

            return $response;

        } catch (SignatureVerificationException $e) {
            return [
                'error' => $e->getMessage()
            ];
        } catch (UnexpectedValueException $e) {
            return [
                'error' => $e->getMessage()
            ];
        } catch (PDOException $e) {
            return [
                'error' => DatabaseErrorHelpers::error($e)
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }catch(RouteNotFoundException $e){
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}
