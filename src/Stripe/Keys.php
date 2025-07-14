<?php

namespace App\Stripe;

require_once __DIR__ . "/../../config.php";

class Keys{
    private static $SECRET_KEY = STRIPE_SECRET_KEY;

    private static $PUBLISH_KEY = STRIPE_PUBLISH_KEY;

    public static function getSecretKey():string
    {
        return self::$SECRET_KEY;
    }

    public static function getPublishKey():string
    {
        return self::$PUBLISH_KEY;
    }



}