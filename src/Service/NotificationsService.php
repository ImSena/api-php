<?php

namespace App\Service;

use App\Service\Base\BaseService;
use App\Service\StoreService;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../../config.php';

class NotificationsService extends BaseService
{

    public function notifyOrderCreated(array $orderData)
    {
        return $this->execute(function () use ($orderData) {
            $emailStore = new EmailStoreService($this->pdo);
            $emailAdmin = $emailStore->getEmailDefault();

            if (isset($emailAdmin['error'])) {
                throw new Exception("Não foi possível resgatar e-mail do lojista");
            }

            $emailAdmin = $emailAdmin['email'];

            $send = $this->getNotifier()->sendOrderCreated($orderData, $orderData['email']);

            if (!$send) {
                throw new Exception("Não foi possível enviar e-mail para o usuário: " . $orderData['email']);
            }

            $send = $this->getNotifier()->sendOrderCreated($orderData, $emailAdmin, "ADMIN");

            if (!$send) {
                throw new Exception("Não foi possível enviar e-mail para o lojista");
            }

            return true;
        });
    }

    public function notifyOrderPaySuccess(array $orderData)
    {
        return $this->execute(function () use ($orderData) {
            $emailStore = new EmailStoreService($this->pdo);
            $emailAdmin = $emailStore->getEmailDefault();

            if (isset($emailAdmin['error'])) {
                throw new Exception("Não foi possível resgatar o e-mail do lojista");
            }

            $emailAdmin = $emailAdmin['email'];

            $send = $this->getNotifier()->sendPaymentConfirmed($orderData, $orderData['email']);

            if (!$send) {
                throw new Exception("Não foi possivel enviar e-mail para o usuário: " . $orderData['email']);
            }

            $send = $this->getNotifier()->sendPaymentConfirmed($orderData, $emailAdmin, "ADMIN");

            if (!$send) {
                throw new Exception("Não foi possível enviar o e-mail para o cliente");
            }

            return true;
        });
    }

    public function notifyOrderPayFailed(array $orderData)
    {
        return $this->execute(function () use ($orderData) {
            $emailStore = new EmailStoreService($this->pdo);
            $emailAdmin = $emailStore->getEmailDefault();

            if (isset($emailAdmin['error'])) {
                throw new Exception("Não foi possível resgatar o e-mail do lojista");
            }

            $emailAdmin = $emailAdmin['email'];

            $send = $this->getNotifier()->sendPaymentDenied($orderData, $orderData['email']);

            if (!$send) {
                throw new Exception("Não foi possivel enviar e-mail para o usuário: " . $orderData['email']);
            }

            $send = $this->getNotifier()->sendPaymentDenied($orderData, $emailAdmin, "ADMIN");

            if (!$send) {
                throw new Exception("Não foi possível enviar o e-mail para o cliente");
            }

            return true;
        });
    }

    public function notifyOrderShipped(array $orderData, ?array $files = null)
    {
        return $this->execute(function () use ($orderData, $files) {
            $emailStore = new EmailStoreService($this->pdo);
            $emailAdmin = $emailStore->getEmailDefault();

            if (isset($emailAdmin['error'])) {
                throw new Exception("Não foi possível resgatar o e-mail do lojista");
            }

            $emailAdmin = $emailAdmin['email'];

            $send = $this->getNotifier()->sendOrderShipped($orderData, $orderData['email'], "USER", $files);

            if (!$send) {
                throw new Exception("Não foi possivel enviar e-mail para o usuário: " . $orderData['email']);
            }

            $send = $this->getNotifier()->sendOrderShipped($orderData, $emailAdmin, "ADMIN", $files);

            if (!$send) {
                throw new Exception("Não foi possível enviar o e-mail para o cliente");
            }

            return true;
        });
    }

    public function notifyOrderDelivered(array $orderData, ?array $files = null)
    {
        return $this->execute(function () use ($orderData, $files) {
            $emailStore = new EmailStoreService($this->pdo);
            $emailAdmin = $emailStore->getEmailDefault();

            if (isset($emailAdmin['error'])) {
                throw new Exception("Não foi possível resgatar o e-mail do lojista");
            }

            $emailAdmin = $emailAdmin['email'];

            $send = $this->getNotifier()->sendOrderDeliverd($orderData, $orderData['email'], "USER", $files);

            if (!$send) {
                throw new Exception("Não foi possivel enviar e-mail para o usuário: " . $orderData['email']);
            }

            $send = $this->getNotifier()->sendOrderDeliverd($orderData, $emailAdmin, "ADMIN", $files);

            if (!$send) {
                throw new Exception("Não foi possível enviar o e-mail para o cliente");
            }

            return true;
        });
    }

    public function sendNotificationsClient(string $subject, string $email)
    {
        return $this->execute(function () use ($subject, $email) {
            $subjects = [
                'PAYMENT_SUCCESS',
                'PAYMENT_CANCELED',
            ];

            if (!in_array($subject, $subjects)) {
                throw new Exception("Assunto não disponível para envio de email");
            }

            $contentEmail = $this->getContentEmail($subject);

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = HOST_EMAIL;
            $mail->SMTPAuth = true;
            $mail->Port = 587;
            $mail->SMTPSecure = false;
            $mail->SMTPAutoTLS = false;
            $mail->Username = USERNAME_MAIL;
            $mail->Password = PASSWORD_MAIL;
            $mail->CharSet = 'UTF-8';
            $mail->From     = "no-reply@escalaweb.com.br";
            $mail->FromName = "Escala Web";
            $mail->addAddress($email);
            $mail->WordWrap = 50;
            $mail->isHTML(true);

            $subject = $contentEmail['subject'];

            $mail->Subject = $subject;

            $mail->Body = $contentEmail['html'];

            return $mail->Send();
        });
    }

    private function getContentEmail($subject)
    {
        switch ($subject) {
            case "PAYMENT_SUCCESS":
                $subject_email = "Pagamento confirmado";
                $title = "Obrigado pelo seu pagamento!";
                $message = "Recebemos seu pagamento com sucesso. Sua compra está sendo processada e em breve você receberá mais detalhes.";
                break;

            case "PAYMENT_FAILED":
                $subject_email = "Falha no pagamento";
                $title = "Ops, algo deu errado!";
                $message = "Não conseguimos processar seu pagamento. Verifique seus dados ou tente novamente mais tarde.";
                break;
            case "NEW_ORDER":
                $subject_email = "Novo pedido recebido";
                $title = "Você tem um novo pedido!";
                $message = "Um novo pedido foi realizado em sua loja. Acesse seu painel para ver os detalhes.";
                break;

            case "ORDER_SHIPPED":
                $subject_email = "Pedido enviado";
                $title = "Seu pedido está a caminho!";
                $message = "Seu pedido foi despachado e está a caminho. Em breve você o receberá no endereço informado.";
                break;

            default:
                return false;
        }

        $html = '
            <html>
            <head>
                <meta charset="UTF-8">
                <title>' . htmlspecialchars($subject_email) . '</title>
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f2f2f2; padding: 20px; }
                    .container { background: #fff; max-width: 600px; margin: auto; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                    h1 { color: #333; }
                    p { font-size: 16px; color: #555; }
                    .footer { margin-top: 30px; font-size: 12px; color: #aaa; text-align: center; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>' . htmlspecialchars($title) . '</h1>
                    <p>' . nl2br(htmlspecialchars($message)) . '</p>
                    <div class="footer">Este é um e-mail automático, por favor não responda.</div>
                </div>
            </body>
            </html>';

        return [
            'subject' => $subject_email,
            'html' => $html
        ];
    }

    public function sendNotificationsAdmin(string $subject, array $info)
    {
        return $this->execute(function () use ($subject, $info) {
            $Admin = new AdminService($this->pdo);
            $Store = new StoreService($this->pdo);

            $AdminResult = $Admin->getInfoAdmin();
            $StoreResult = $Store->getInfoStore();

            $subjects = [
                'ORDER_PROCESSING',
            ];

            if (!in_array($subject, $subjects)) {
                throw new Exception("Assunto não disponível para envio de email");
            }

            $contentEmail = $this->getContentEmailAdmin($subject, $info);

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = HOST_EMAIL;
            $mail->SMTPAuth = true;
            $mail->Port = 587;
            $mail->SMTPSecure = false;
            $mail->SMTPAutoTLS = false;
            $mail->Username = USERNAME_MAIL;
            $mail->Password = PASSWORD_MAIL;
            $mail->CharSet = 'UTF-8';
            $mail->From     = "no-reply@" . $StoreResult['domain'];
            $mail->FromName = "Escala Web";
            $mail->addAddress($AdminResult['email']);
            $mail->WordWrap = 50;
            $mail->isHTML(true);

            $subject = $contentEmail['subject'];

            $mail->Subject = $subject;

            $mail->Body = $contentEmail['html'];

            return $mail->Send();
        });
    }

    private function getContentEmailAdmin(string $subject, array $info)
    {
        switch ($subject) {
            case "ORDER_PROCESSING":
                $subject_email = "Pagamento confirmado";
                $title = "Pagamento confirmado para o cliente";
                $message = "Foi recebido um pagamento para o pedido, por favor, levá-lo aos correios para dar prosseguimento";
                break;

            case "PAYMENT_FAILED":
                $subject_email = "Falha no pagamento";
                $title = "Ops, algo deu errado!";
                $message = "Não conseguimos processar seu pagamento. Verifique seus dados ou tente novamente mais tarde.";
                break;
            case "NEW_ORDER":
                $subject_email = "Novo pedido recebido";
                $title = "Você tem um novo pedido!";
                $message = "Um novo pedido foi realizado em sua loja. Acesse seu painel para ver os detalhes.";
                break;

            case "ORDER_SHIPPED":
                $subject_email = "Pedido enviado";
                $title = "Seu pedido está a caminho!";
                $message = "Seu pedido foi despachado e está a caminho. Em breve você o receberá no endereço informado.";
                break;

            default:
                return false;
        }

        $html = '
            <html>
            <head>
                <meta charset="UTF-8">
                <title>' . htmlspecialchars($subject_email) . '</title>
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f2f2f2; padding: 20px; }
                    .container { background: #fff; max-width: 600px; margin: auto; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                    h1 { color: #333; }
                    p { font-size: 16px; color: #555; }
                    .footer { margin-top: 30px; font-size: 12px; color: #aaa; text-align: center; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>' . htmlspecialchars($title) . '</h1>
                    <p>' . nl2br(htmlspecialchars($message)) . '</p>
                    <div class="footer">Este é um e-mail automático, por favor não responda.</div>
                </div>
            </body>
            </html>';

        return [
            'subject' => $subject_email,
            'html' => $html
        ];
    }
}
