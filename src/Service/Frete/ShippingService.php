<?php

namespace App\Service\Frete;

use App\Jwt\JwtAuth;
use App\Model\Frete\Shipping;
use App\Model\Store;
use App\Service\Base\BaseService;
use App\Service\ProductService;
use App\Utils\Validator;
use Exception;
use PDO;

require_once __DIR__ . "/../../../config.php";

class ShippingService extends BaseService
{
    private bool $production;
    private string $url;

    private string $token;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->production = IS_PRODUCTION;
        $this->url = $this->production ?
            'https://melhorenvio.com.br/api/v2/me/'
            : 'https://sandbox.melhorenvio.com.br/api/v2/me/';
        $this->token = $this->getTokenShipping();
    }

    private function getTokenShipping()
    {
        return $this->execute(function () {
            $Shipping = new Shipping($this->pdo);

            $result = $Shipping->getTokenShipping();

            return $result['token_shipping'];
        });
    }

    private function getPostalCodeStore()
    {
        return $this->execute(function () {
            $Store = new Store($this->pdo);
            $addresses = $Store->getAddress();

            $address = array_filter($addresses, function ($address) {
                return $address['is_default'] == 1;
            });

            $address = reset($address);

            return $address['zip_code'];
        });
    }

    public function getQuote(array $data)
    {
        return $this->execute(function () use ($data) {
            $fields = Validator::validate([
                "zip_code" => $data['zip_code'] ?? '',
                "products" => $data['products'] ?? ''
            ]);

            if (!is_array($fields['products'])) {
                throw new Exception("Os campos [id_product, quantity] são obrigatórios");
            }

            foreach ($fields['products'] as $produ) {
                Validator::validate([
                    "id_product" => $produ['id_product'] ?? '',
                    "quantity" => $produ['quantity'] ?? ''
                ]);
            }


            $client = new \GuzzleHttp\Client();
            $postalCodeStore = $this->getPostalCodeStore();
            $ProductService = new ProductService($this->pdo);

            $products = [];

            foreach ($data['products'] as $prod) {
                $product = $ProductService->getProductQuote($prod['id_product']);

                $products = [
                    "id" => $product['id_product_variant'],
                    "width" => $product['weight'],
                    "height" => $product['height'],
                    "length" => $product['length'],
                    "weight" => $product['weight'],
                    "insurance_value" => $product['price'],
                    "quantity" => $prod['quantity']
                ];
            }

            $body = [
                "from" => [
                    "postal_code" => $postalCodeStore,
                ],
                "to" => [
                    "postal_code" => $data['zip_code']
                ],
                "products" => [
                    $products
                ]
            ];

            $response = $client->request('POST', $this->url . 'shipment/calculate', [
                'json' => $body,
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json',
                    'User-Agent' => COMPANY_PROJECT_NAME . "(" . EMAIL_SUPORTE_COMPANY . ")"
                ],
                'verify' => $this->production
            ]);

            $shippings = json_decode($response->getBody()->getContents(), true);

            foreach ($shippings as &$shipping) {
                $service = json_encode($shipping);
                $shipping['signature_eccomerce'] = JwtAuth::renderSignatureShipping($service);
            }

            return $shippings;
        });
    }
}
