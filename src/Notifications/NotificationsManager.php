<?php

namespace App\Notifications;

use App\Interfaces\Notifications\INotifier;
use App\Notifications\Factory\EmailProviderFactory;
use App\Service\StoreService;
use PDO;

class NotificationsManager
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getDefaultNotifier(): INotifier
    {
        $storeService = new StoreService($this->pdo);

        $result = $storeService->getAssets();

        $fromName = $result['NAME_STORE'];
        $email = array_values(array_filter($result['EMAILS'], function ($email) {
            return isset($email['is_default']) && $email['is_default'] == 1;
        }));

        $email = $email[0]['email'] ?? null;

        return EmailProviderFactory::make($email, $fromName);
    }
}
