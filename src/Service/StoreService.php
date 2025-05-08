<?php

namespace App\Service;

use App\Model\Store;
use App\Service\Base\BaseService;
use App\Stripe\Keys;
use App\Utils\Validator;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Stripe;
use Exception;

class StoreService extends BaseService
{
    private $ITypePhones = [
        "WHATSAPP",
        "CELLPHONE",
        "PHONE",
        "BUSINESS"
    ];

    private $ITypeSociais = [
        "INSTAGRAM",
        "FACEBOOK",
        "YOUTUBE",
        "LINKEDIN",
        "TIKTOK",
        "X"
    ];

    private $ITypeIdentities = [
        'LOGO',
        'LOGO_FOOTER',
        'FAVICON',
    ];

    public function createStore(array $data)
    {
        return $this->execute(function () use ($data) {
            $Store = new Store($this->pdo);
            $AddressStoreService = new AddressStoreService($this->pdo);
            $PhoneStoreService = new PhoneStoreService($this->pdo);
            $EmailStoreService = new EmailStoreService($this->pdo);
            $SociaisStoreService = new SocialStoreService($this->pdo);
            $StoreMediaService = new StoreMediaService($this->pdo);
            
            $storeCreated = $Store->getActiveStore();
            
            
            if ($storeCreated) {
                throw new Exception("Loja já cadastrada");
            }
            
            $inactiveStores = $Store->setStatuStores();
            
            if (!$inactiveStores) {
                throw new Exception("Não foi possível criar loja");
            }
            
            $fields = $this->validateFieldsStore($data);
            
            $resultStore = $Store->createStore($fields);
            
            if (isset($resultStore['error'])) {
                throw new Exception("Não foi possível criar loja");
            }
            
            foreach($fields['addresses'] as $address){
                $resultAddress = $AddressStoreService->createAddress($address);

                if(isset($resultAddress['error'])){
                    throw new Exception("Não foi possível criar loja: endereço");
                }
            }

            if(!empty($fields['phones'])){
                foreach($fields['phones'] as $phone){
    
                    $resultPhone = $PhoneStoreService->createPhoneStore($phone);
    
                    if(isset($resultPhone['error'])){
                        throw new Exception("Não foi possível criar loja: Phone");
                    }
                }
            }

            if(!empty($fields['emails'])){
                foreach($fields['emails'] as $email){
    
                    $resultEmail = $EmailStoreService->createEmail($email);
    
                    if(isset($resultEmail['error'])){
                        throw new Exception("Não foi possível criar loja: emails");
                    }
                }
            }


            if(!empty($fields['sociais'])){
                foreach($fields['sociais'] as $social){
    
                    $resultSocial = $SociaisStoreService->createSocial($social);
    
                    if(isset($resultSocial['error'])){
                        throw new Exception("Não foi possível criar loja: social");
                    }
                }
            }

            if(!empty($fields['identity'])){
                foreach($fields['identity'] as $identity){
                    $result = $StoreMediaService->createMedia($identity);
    
                    if(isset($result['error'])){
                        throw new Exception("Não foi possível criar loja: identity");
                    }
                }
            }

            return "Loja cadastrada com sucesso";
        }, true);
    }

    private function validateFieldsStore(array $data): array
    {
        $fields = Validator::validate([
            "name" => $data['name'] ?? '',
            "template" => $data['template'] ?? 'template01',
            "pallete" => $data['pallete'] ?? "Gold",
        ]);

        $fields['addresses'] = null;
        $fields['id_analitycs'] = null;
        $fields['id_search_console'] = null;
        $fields['id_tag_manager'] = null;
        $fields['phones'] = null;
        $fields['emails'] = null;
        $fields['sociais'] = null;
        $fields['identity'] = null;

        if (isset($data['id_analitycs']) && !empty($data['id_analitycs'])) {
            $fields['id_analitycs'] = $data['id_analitycs'];
        }

        if (isset($data['id_search_console']) && !empty($data['id_search_console'])) {
            $fields['id_search_console'] = $data['id_search_console'];
        }

        if (isset($data['id_tag_manager']) && !empty($data['id_tag_manager'])) {
            $fields['id_tag_manager'] = $data['id_tag_manager'];
        }

        if (isset($data['addresses']) && !empty($data['addresses'])) {
            $addresses = [];

            foreach ($data['addresses'] as $address) {
                $addressData = Validator::validate([
                    "public_area" => $address['public_area'] ?? '',
                    "number" => $address['number'] ?? '',
                    "district" => $address['district'] ?? '',
                    "city" => $address['city'] ?? '',
                    "state" => $address['state'] ?? '',
                    "zip_code" => $address['zip_code'] ?? '',
                    "is_default" => $address['is_default'] ?? '',
                    "is_show" => $address['is_show'] ?? '',
                ]);

                $addressData['complement'] = isset($address['complement']) && !empty($address['complement']) ? $address['complement'] : null;

                $addresses[] = $addressData;
            }

            $fields['addresses'] = $addresses;
        }else{
            throw new Exception("O campo [addresses] é obrigatório.");
        }

        if (isset($data['phones']) && !empty($data['phones'])) {
            $phones = [];

            foreach ($data['phones'] as $phone) {
                if (!in_array($phone['type'], $this->ITypePhones)) {
                    $types = implode(", ", $this->ITypePhones);
                    throw new Exception("Tipo de phone está incorreto! Tipos permitidos [" . $types . "]");
                }

                $phones[] = Validator::validate([
                    "type" => $phone['type'] ?? '',
                    "number" => $phone['number'] ?? '',
                    "is_default" => $phone['is_default'] ?? '',
                    "is_show" => $phone['is_show'] ?? ''
                ]);
            }

            $fields['phones'] = $phones;
        }

        if (isset($data['emails']) && !empty($data['emails'])) {
            $emails = [];

            foreach ($data['emails'] as $email) {
                $emails[] = Validator::validate([
                    "email" => $email['email'] ?? '',
                    "is_default" => $email['is_default'] ?? '',
                    "is_show" => $email['is_show'] ?? ''
                ]);
            }

            $fields['emails'] = $emails;
        }

        if (isset($data['sociais']) && !empty($data['sociais'])) {
            $sociais = [];

            foreach ($data['sociais'] as $social) {
                if (!in_array($social['type'], $this->ITypeSociais)) {
                    $types = implode(", ", $this->ITypeSociais);
                    throw new Exception("Tipo de phone está incorreto! Tipos permitidos [" . $types . "]");
                }

                $sociais[] = Validator::validate([
                    "type" => $social['type'] ?? '',
                    "link" => $social['link'] ?? ''
                ]);
            }

            $fields['sociais'] = $sociais;
        }

        if (isset($data['token_shipping']) && !empty($data['token_shipping'])) {
            $fields['token_shipping'] = $data['token_shipping'];
        }

        if (isset($data['identity']) && !empty($data['identity'])) {
            $identities = [];

            foreach ($data['identity'] as $identity) {
                if (!in_array($identity['type'], $this->ITypeIdentities)) {
                    $types = implode(", ", $this->ITypeIdentities);
                    throw new Exception("Tipo de phone está incorreto! Tipos permitidos [" . $types . "]");
                }

                $identities[] = Validator::validate([
                    "type" => $identity['type'] ?? '',
                    "id_media" => $identity['id_media'] ?? ''
                ]);
            }

            $fields['identity'] = $identities;
        }

        $fields['domain'] = $this->getDomain($_SERVER['SERVER_NAME']);

        return $fields;
    }

    private function getDomain(string $host): string
    {
        $new_host = str_replace("api.", "", $host);
        return $new_host;
    }

    public function startOnboardingProcess()
    {
        return $this->execute(function () {
            Stripe::setApiKey(Keys::getSecretKey());

            $storeModel = new Store($this->pdo);
            $store = $storeModel->getActiveStore();

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
        });
    }

    public function createLogin()
    {
        return $this->execute(function () {
            Stripe::setApiKey(Keys::getSecretKey());

            $storeModel = new Store($this->pdo);
            $store = $storeModel->getActiveStore();

            if (!$store) {
                throw new Exception("Não foi possível encontrar lojista");
            }

            $loginLink = Account::createLoginLink($store['stripe_account_id']);

            return ['url' => $loginLink->url, 'message' => "Link gerado com sucesso"];
        });
    }

    public function getInfoStore()
    {
        return $this->execute(function () {
            $Store = new Store($this->pdo);

            $result = $Store->getInfoStore();

            if (!$result) {
                throw new Exception("Não foi possível resgatar informações da loja");
            }


            return $result;
        });
    }

    public function getAssets()
    {
        return $this->execute(function () {

            $Store = new Store($this->pdo);

            $result = $Store->getActiveStore();

            return [
                "THEME" => !empty($result['pallete']) ? $result['pallete'] : 'Gold',
                "LAYOUT" => !empty($result['template']) ? $result['template'] : 'template01',
                "ID_ANALITYCS" => $result['id_analitycs'] ?? '',
                "ID_SEARCH_CONSOLE" => $result['id_search_console'] ?? '',
                "ID_TAG_MANAGER" => $result['id_tag_manager'] ?? '',
                "ADDRESSES" => $result['address'] ?? '',
                "PHONES" => $result['phones'] ?? '',
                "EMAILS" => $result['emails'] ?? '',
                "SOCIAIS" => $result['social'] ?? '',
                "IS_QUOTE" => !empty($result['is_quote']) ? $result['is_quote'] : false
            ];
        });
    }
}
