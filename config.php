<?php
define('IS_PRODUCTION', false);

if(IS_PRODUCTION){
    define('HOST', 'mysql.nsararidades.com.br');
    define('DBNAME', 'nsararidades');
    define('USERNAME', 'nsararidades');
    define('PASSWORD', 'JC6qbSz');
    
    //constantes para o checkout
    define("SUCCESS_URL", "https://nsararidades/customer/checkout");
    define('CANCEL_URL', 'https://nsararidades/');
    
    define('HOST_EMAIL', 'smtp.escalaweb.com.br');
    define('USERNAME_MAIL', 'teste@escalaweb.com.br');
    define('PASSWORD_MAIL', 'Escalaweb$17');


    define("STRIPE_SECRET_KEY", "sk_test_51R4nKhQmFXmPIJlZEWHlgKRhYK9IIikco33jWa1gYE71i0Gu4z8qaDGoaMtdKQiC9ACWNUC7YEczMxV3ZvCXkgwO00BmNUeFAu");
    define("STRIPE_PUBLISH_KEY", "pk_test_51R4nKhQmFXmPIJlZbib44LnPjJOQv3mb1NAVzfJgJDt27RC8X1aWhawFnAbm9hvFpGAIJEs5DG2Msci75ymLvIZV00WVGQ4qD7");
    define("URL_EMAIL", "https://nsararidades/");

}else{
    //constantes para banco
    define('HOST', 'localhost');
    define('DBNAME', 'ecommerce');
    define('USERNAME', 'root');
    define('PASSWORD', '');
    
    //constantes para o checkout
    define("SUCCESS_URL", "http://192.168.15.4:5173/customer/checkout");
    define('CANCEL_URL', 'http://192.168.15.4:5173/');
    
    define('HOST_EMAIL', 'smtp.escalaweb.com.br');
    define('USERNAME_MAIL', 'teste@escalaweb.com.br');
    define('PASSWORD_MAIL', 'Escalaweb$17');

    define("STRIPE_SECRET_KEY", "sk_test_51R4nKhQmFXmPIJlZEWHlgKRhYK9IIikco33jWa1gYE71i0Gu4z8qaDGoaMtdKQiC9ACWNUC7YEczMxV3ZvCXkgwO00BmNUeFAu");
    define("STRIPE_PUBLISH_KEY", "pk_test_51R4nKhQmFXmPIJlZbib44LnPjJOQv3mb1NAVzfJgJDt27RC8X1aWhawFnAbm9hvFpGAIJEs5DG2Msci75ymLvIZV00WVGQ4qD7");
    define("URL_EMAIL", "http://localhost:5173/");
}

define('PHOTO', __DIR__ .'/');
define('TOOLS', __DIR__.'/src/tools');
define("PATH", realpath(__DIR__));
define("MEDIA", PATH."/uploads/");
define('SECRET_KEY', 'k!v9X3o5@zTmFc7cQ^wL5kE2bD8jZb0N');
define("COMPANY_PROJECT_NAME", "Escala Web");
define("EMAIL_SUPORTE_COMPANY", "suporte@escalaweb.com.br");
