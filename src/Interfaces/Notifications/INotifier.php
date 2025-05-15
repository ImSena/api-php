<?php

namespace App\Interfaces\Notifications;

interface INotifier
{
    public function sendOrderCreated(array $order, string $recipientEmail):bool;
    public function sendPaymentConfirmed(array $order, string $recipientEmail): bool;
    public function sendPaymentDenied(array $order, string $recipientEmail): bool;
    public function sendOrderShipped(array $order, string $recipientEmail): bool;

    public function sendOrderDeliverd(array $order, string $recipientEmail):bool;
}