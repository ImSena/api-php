<?php

namespace App\Stripe;

class Keys{
    private static $SECRET_KEY = "sk_test_51R4nKhQmFXmPIJlZEWHlgKRhYK9IIikco33jWa1gYE71i0Gu4z8qaDGoaMtdKQiC9ACWNUC7YEczMxV3ZvCXkgwO00BmNUeFAu";

    private static $PUBLISH_KEY = "pk_test_51R4nKhQmFXmPIJlZbib44LnPjJOQv3mb1NAVzfJgJDt27RC8X1aWhawFnAbm9hvFpGAIJEs5DG2Msci75ymLvIZV00WVGQ4qD7";

    public static function getSecretKey():string
    {
        return self::$SECRET_KEY;
    }

    public static function getPublishKey():string
    {
        return self::$PUBLISH_KEY;
    }



}