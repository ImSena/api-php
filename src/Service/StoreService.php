<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Store;
use App\Stripe\Keys;
use App\Utils\Validator;
use Exception;
use PDO;
use PDOException;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Stripe;

class StoreService
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createStore(array $data)
    {
        try {
            $fields = Validator::validate([
                "store_name" => $data['store_name'] ?? ''
            ]);

            $fields['domain'] = $this->getDomain($_SERVER['SERVER_NAME']);

            $Store = new Store($this->pdo);

            $result = $Store->createStore($fields);

            if (!$result) {
                throw new Exception("Não foi possível criar loja");
            }

            return "Loja cadastrada com sucesso";
        } catch (PDOException $e) {
            return [
                'error' => DatabaseErrorHelpers::error($e)
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    private function getDomain(string $host): string
    {
        $new_host = str_replace("api.", "", $host);
        return $new_host;
    }

    public function startOnboardingProcess(string $storeId)
    {
        try {
            Stripe::setApiKey(Keys::getSecretKey());

            $storeModel = new Store($this->pdo);
            $store = $storeModel->findById($storeId);

            if (!$store) {
                throw new Exception("Lojista não encontrado. ");
            }

            if (empty($store['stripe_account_id'])) {
                $account = Account::create([
                    'type' => 'express',
                    'country' => 'BR',
                    'capabilities' => [
                        'card_payments' => ['requested' => true],
                        'transfers' => ['requested' => true],
                        'boleto_payments' => ['requested' => true]
                    ]
                ]);

                $data = [
                    'id_store' => $storeId,
                    'stripe_account_id' => $account->id
                ];

                $storeModel->updateAccount($data);
                $accountId = $account->id;
            } else {
                $accountId = $store['stripe_account_id'];
            }

            $accountLink = AccountLink::create([
                'account' => $accountId,
                'refresh_url' => 'http://escalaweb.com.br/',
                'return_url' => 'http://escalaweb.com.br/',
                'type' => 'account_onboarding',
            ]);

            return ['url' => $accountLink->url, 'message' => "Processo onboarding iniciado."];
        } catch (PDOException $e) {
            return [
                'error' => DatabaseErrorHelpers::error($e)
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    public function createLogin(string $storeId)
    {
        try {

            Stripe::setApiKey(Keys::getSecretKey());

            $storeModel = new Store($this->pdo);
            $store = $storeModel->findById($storeId);

            if (!$store) {
                throw new Exception("Não foi possível encontrar lojista");
            }

            $loginLink = Account::createLoginLink($store['stripe_account_id']);

            return ['url' => $loginLink->url, 'message' => "Link gerado com sucesso"];
        } catch (PDOException $e) {
            return [
                'error' => DatabaseErrorHelpers::error($e)
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    public function getInfoStore()
    {
        try {

            $Store = new Store($this->pdo);

            $result = $Store->getInfoStore();

            if (!$result) {
                throw new Exception("Não foi possível resgatar informações da loja");
            }


            return $result;
        } catch (PDOException $e) {
            return [
                'error' => DatabaseErrorHelpers::error($e)
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}
