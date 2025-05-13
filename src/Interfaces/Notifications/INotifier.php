<?php

namespace App\Interfaces\Notifications;

interface INotifier
{
    public function sendOrderCreated(array $order, string $recipientEmail):bool;
    public function sendOrderCompleted(array $order, string $recipientEmail):bool;

    public function sendStatusOrder(array $order, string $subject, string $recipientEmail): bool;
}