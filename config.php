<?php

//constantes para banco
define('HOST', 'localhost');
define('DBNAME', 'ecommerce');
define('USERNAME', 'root');
define('PASSWORD', '');

//paths
define('PHOTO', __DIR__ .'/');
define('TOOLS', __DIR__.'/src/tools');
define("PATH", realpath(__DIR__));
define("MEDIA", PATH."/uploads/");

//constantes para o checkout
define("SUCCESS_URL", "http://192.168.15.4:5173/customer/checkout");
define('CANCEL_URL', 'http://192.168.15.4:5173/');

define('HOST_EMAIL', 'smtp.escalaweb.com.br');
define('USERNAME_MAIL', 'teste@escalaweb.com.br');
define('PASSWORD_MAIL', 'Escalaweb$17');
