<?php

namespace App\Service\Stripe;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Stripe\Store;
use App\Stripe\Keys;
use App\Utils\Validator;
use Exception;
use PDOException;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Stripe;

class StoreService{
    
    public static function createStore(array $data){
        try{

            $fields = Validator::validate([
                "store_name" => $data['name'] ?? ''
            ]);

            $fields['domain'] = $_SERVER['SERVER_NAME'];

            $Store = new Store();

            $result = $Store->createStore($fields);

            return "Loja cadastrada com sucesso";
        }catch(PDOException $e){
            return [
                'error' => DatabaseErrorHelpers::error($e)
            ];
        }catch(Exception $e){
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    public static function startOnboardingProcess(string $storeId){
        try{
            Stripe::setApiKey(Keys::getSecretKey());

            $storeModel = new Store();
            $store = $storeModel->findById($storeId);

            if(!$store){
                throw new Exception("Lojista não encontrado. ");
            }

            if(empty($store['stripe_account_id'])){
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
            }else{
                $accountId = $store['stripe_account_id'];
            }

            $accountLink = AccountLink::create([
                'account' => $accountId,
                'refresh_url' => 'http://escalaweb.com.br/',
                'return_url' => 'http://escalaweb.com.br/',
                'type' => 'account_onboarding',
            ]);
            
            return ['url' => $accountLink->url, 'message' => "Processo onboarding iniciado."];

        }catch(PDOException $e){
            return [
                'error' => DatabaseErrorHelpers::error($e)
            ];
        }catch(Exception $e){
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    public static function createLogin(string $storeId){
        try{

            Stripe::setApiKey(Keys::getSecretKey());

            $storeModel = new Store();
            $store = $storeModel->findById($storeId);

            if(!$store){
                throw new Exception("Não foi possível encontrar lojista");
            }

            $loginLink = Account::createLoginLink($store['stripe_account_id']);

            return ['url' => $loginLink->url, 'message' => "Link gerado com sucesso"];

        }catch(PDOException $e){
            return [
                'error' => DatabaseErrorHelpers::error($e)
            ];
        }catch(Exception $e){
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}