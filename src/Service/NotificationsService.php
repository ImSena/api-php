<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Service\Stripe\StoreService;
use Exception;
use PDO;
use PDOException;
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../../config.php';

class NotificationsService
{
    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function sendNotificationsClient(string $subject, string $email)
    {
        try {
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
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        } catch (PDOException $e) {
            return [
                'error' => DatabaseErrorHelpers::error($e)
            ];
        }
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

            case "SUBSCRIPTION_EXPIRED":
                $subject_email = "Sua assinatura expirou";
                $title = "Sua assinatura chegou ao fim";
                $message = "Sua assinatura expirou. Para continuar aproveitando nossos serviços, renove agora mesmo.";
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
        try {

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
            $mail->From     = "no-reply@".$StoreResult['domain'];
            $mail->FromName = "Escala Web";
            $mail->addAddress($AdminResult['email']);
            $mail->WordWrap = 50;
            $mail->isHTML(true);

            $subject = $contentEmail['subject'];

            $mail->Subject = $subject;

            $mail->Body = $contentEmail['html'];

            return $mail->Send();
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        } catch (PDOException $e) {
            return [
                'error' => DatabaseErrorHelpers::error($e)
            ];
        }
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

            case "SUBSCRIPTION_EXPIRED":
                $subject_email = "Sua assinatura expirou";
                $title = "Sua assinatura chegou ao fim";
                $message = "Sua assinatura expirou. Para continuar aproveitando nossos serviços, renove agora mesmo.";
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
