<?php

namespace App\Notifications;

use App\Interfaces\Notifications\INotifier;
use App\Notifications\Factory\EmailProviderFactory;
use App\Service\StoreService;
use PDO;

require_once __DIR__ . "/../../config.php";
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

        $email = explode("@", $email);
        $email = "no-reply@".$email[1];

        $ano = date("Y");

        $data_email = [
            "url_logo" => "https://nsararidades.com.br/assets/logo-D20lBSqG.png",
            "company" => "NSA Raridades",
            "date" => $ano,
            "date_hour" => date("Y-m-d H:i:s"),
            "link_eccomerce" => URL_STORE,
            "link_policy" => "https://escalaweb.com.br/politica-de-privacidade",
            "link_contact" => URL_STORE.'/contato',
            "link_painel" => URL_STORE.'/administrativo',
            "store_url" => URL_STORE
        ];

        return EmailProviderFactory::make($email, $fromName, $data_email);
    }
}
