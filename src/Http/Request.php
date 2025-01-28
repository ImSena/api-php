<?php

namespace App\Http;
class Request
{
    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    private static function isMultipart():bool
    {
        return strpos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') !== false;
    }
    public static function body():array
    {
        $method = self::method();

        if($method != 'GET'){
            if(self::isMultipart()){
                return $_POST;
            }else{
                $json = json_decode(file_get_contents('php://input'), true) ?? [];
                return $json;
            }
        }         
        return $method === 'GET' ? $_GET : [];
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

    public static function files():array
    {

        $files = [];


        foreach($_FILES as $key => $file){
            if(!is_array($file['name'])){
                $files[$key] = [
                    'name' => $file['name'],
                    'type' => $file['type'],
                    'tmp_name' => $file['tmp_name'],
                    'error' => $file['error'],
                    'size' => $file['size']
                ];
            }else{
                foreach($file['name'] as $index => $name){
                    $files[$key][$index] = [
                        'name' => $name,
                        'type' => $file['type'][$index],
                        'tmp_name' => $file['tmp_name'][$index],
                        'error' => $file['error'][$index],
                        'size' => $file['size'][$index]
                    ];
                }
            }
        }

       return $files;
    }
}