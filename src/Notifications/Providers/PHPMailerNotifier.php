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

    public function sendOrderCreated(array $order, string $recipientEmail, string $type = "USER"): bool
    {
        $variables = [...$this->var_default_email, ... $order];
        $body = $this->renderTemplate("order_created.twig", $variables, $type);
        return $this->send($recipientEmail, "Pedido criado com sucesso!", $body);
    }

    public function sendPaymentConfirmed(array $order, string $recipientEmail, string $type = "USER"): bool
    {
        $variables = [...$this->var_default_email, ... $order];
        $body = $this->renderTemplate('payment_confirmed.twig', $variables, $type);
        return $this->send($recipientEmail, "Pagamento Confirmado!", $body);
    }

    public function sendPaymentDenied(array $order, string $recipientEmail, string $type = "USER"): bool
    {
        $variables = [...$this->var_default_email, ... $order];
        $body = $this->renderTemplate("payment_denied.twig", $variables, $type);
        return $this->send($recipientEmail, "Pagamento Negado", $body);
    }
    
    public function sendOrderShipped(array $order, string $recipientEmail, string $type = "USER"): bool{
        $variables = [...$this->var_default_email, ... $order];
        $body = $this->renderTemplate("order_shipped.twig", $variables, $type);
        return $this->send($recipientEmail, "Pedido Enviado!", $body);
    }

    public function sendOrderDeliverd(array $order, string $recipientEmail, string $type = "USER"): bool
    {
        $variables = [...$this->var_default_email, ...$order];
        $body = $this->renderTemplate("order_shipped.twig", $variables, $type);
        return $this->send($recipientEmail, "Pedido Entregue!", $body);
    }

    public function sendError(array $error, string $recipientEmail): bool
    {
        $variables = [...$this->var_default_email, ...$error];
        $body = $this->renderTemplate("error.twig", $variables, "ADMIN");
        return $this->send($recipientEmail, "Erro na Loja", $body);
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