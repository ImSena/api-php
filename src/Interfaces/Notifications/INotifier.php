<?php

namespace App\Interfaces\Notifications;

interface INotifier
{
    public function sendOrderCreated(array $order, string $recipientEmail, string $type = "USER", ?array $files = null):bool;
    public function sendPaymentConfirmed(array $order, string $recipientEmail, string $type = "USER", ?array $files = null): bool;
    public function sendPaymentDenied(array $order, string $recipientEmail, string $type = "USER", ?array $files = null): bool;
    public function sendOrderShipped(array $order, string $recipientEmail, string $type = "USER", ?array $files = null): bool;

    public function sendOrderDeliverd(array $order, string $recipientEmail, string $type = "USER", ?array $files = null):bool;

    public function sendError(array $error, string $recipientEmail):bool;

     public function sendStore(array $data, string $recepientEmail): bool;
}