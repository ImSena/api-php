<?php

namespace App\Http;

class Request
{
    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function body():array
    {
        $json = json_decode(file_get_contents('php://input'), true) ?? [];

        $data = match(self::method()){
            'GET' => $_GET,
            'POST', 'PUT', 'DELETE' => $json,
        };
        
        return $data;
    }
    public static function header()
    {
        $headers = getallheaders();

        if(isset($headers['Authorization'])){
            $authorizationHeader = $headers['Authorization'];
            return $authorizationHeader;
        }else{
            return false;
        }
    }
    public static function getToken()
    {
        $header = self::header();

        if(preg_match('/Bearer\s(\S+)/', $header, $matches)){
            $token = $matches[1];
            return $token;
        }else{
            return false;
        }
    }

}