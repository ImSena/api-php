<?php 

namespace App\Notifications\Providers;

use App\Notifications\Base\EmailNotifier;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class PHPMailerNotifier extends EmailNotifier
{
    protected PHPMailer $mailer;

    public function __construct(PHPMailer $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    public function sendOrderCreated(array $order, string $recipientEmail): bool
    {
        $body = $this->renderTemplate("order_created.html", $order);
        return $this->send($recipientEmail, "Pedido criado com sucesso!", $body);
    }

    public function sendOrderCompleted(array $order, string $recipientEmail): bool
    {
        $body = $this->renderTemplate("order_completed.html", $order);
        return $this->send($recipientEmail, "Pedido concluído", $body);
    }

    public function sendStatusOrder(array $order, string $subject, string $recipientEmail): bool
    {
        $body = $this->renderTemplate("change_status_order.html", $order);
        return $this->send($recipientEmail, $subject, $body);
    }

    protected function send(string $to, string $subject, string $body): bool|array
    {
        try{
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->isHTML(true);
            return $this->mailer->send();
        }catch(Exception $e){
            return ['error' => "Não foi possível enviar email: $e"];
        }
    }
}