<?php 

namespace App\Notifications\Providers;

use App\Notifications\Base\EmailNotifier;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class PHPMailerNotifier extends EmailNotifier
{
    protected PHPMailer $mailer;

    public function __construct(PHPMailer $mailer, $var_defaults)
    {
        parent::__construct($var_defaults);
        $this->mailer = $mailer;
    }

    public function sendOrderCreated(array $order, string $recipientEmail): bool
    {
        $variables = [...$this->var_default_email, ... $order];
        $body = $this->renderTemplate("order_created.twig", $variables);
        return $this->send($recipientEmail, "Pedido criado com sucesso!", $body);
    }

    public function sendPaymentConfirmed(array $order, string $recipientEmail): bool
    {
           $variables = [...$this->var_default_email, ... $order];
        $body = $this->renderTemplate('payment_confirmed.twig', $variables);
        return $this->send($recipientEmail, "Pagamento Confirmado!", $body);
    }

    public function sendOrderCompleted(array $order, string $recipientEmail): bool
    {
        $variables = [...$this->var_default_email, ... $order];
        $body = $this->renderTemplate("order_completed.html", $variables);
        return $this->send($recipientEmail, "Pedido concluído", $body);
    }

    public function sendStatusOrder(array $order, string $subject, string $recipientEmail): bool
    {
        $variables = [...$this->var_default_email, ... $order];
        $body = $this->renderTemplate("change_status_order.html", $variables);
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