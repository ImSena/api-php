<?php

namespace App\Helpers;

use App\Utils\FormatterLinkProduct;

class OrderNotificationFormatter
{
    public static function format(array $order, string $emailUser, array $address): array
    {
        $subtotal = (float) $order['total_price'];
        $shipping = (float) $order['shipping']['shipping_cost'];
        $total = $subtotal + $shipping;

        $products = array_map(function ($product) {
            $categoria = FormatterLinkProduct::formatter($product['category']);
            $productName = FormatterLinkProduct::formatter($product['name']);
            $id = $product['id_product_variant'];
            return [
                "product_url" => URL_STORE."/$categoria/$productName/$id",
                "product_image" => URL_PHOTOS . $product['image_path'],
                "product_name" => $product['name'],
                "quantity" => $product['quantity'],
                "price" => 'R$ ' . number_format($product['price'], 2, ',', '.'),
            ];
        }, $order['products']);

        return [
            "id" => $order['id_order'],
            "email" => $emailUser,
            "order_number" => "#".$order['id_order'],
            "order_date" => date("m/d/Y - H:i:s"),
            "order_url" => URL_ORDER . $order['id_order'],
            'items' => $products,
            "subtotal" => 'R$ ' . number_format($subtotal, 2, ',', '.'),
            "shipping" => 'R$ ' . number_format($shipping, 2, ',', '.'),
            "total" => 'R$ ' . number_format($total, 2, ',', '.'),
            "shipping_method" => [
                "carrier" => $order['shipping']['carrier'],
                "service" => 'R$ ' . number_format($shipping, 2, ',', '.')
            ],
            "shipping_address" => [
                "public_area" => $address['public_area'],
                "number" => $address['number'],
                "complement" => $address['complement'],
                "district" => $address['district'],
                "city" => $address['city'],
                "state" => $address['state']
            ]
        ];
    }
}
