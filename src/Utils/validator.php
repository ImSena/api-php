<?php

namespace App\Utils;

use Exception;

class Validator
{
    public static function validate(array $fields)
    {
        $errors = [];

        foreach($fields as $field => $value){
            if(empty(trim($value))){
                $errors[] = $field;
            }
        }

        if(!empty($errors)){

            $qtdErrors = count($errors);

            if($qtdErrors > 1){
                $message = "Os campos [".implode(", ", $errors)."] são obrigatórios";
            }else{
                $message = "O campo [".implode(", ", $errors)."] é obrigatório";
            }

            throw new Exception($message);
        }


        return $fields;
    }

    public static function validateEmail(string $email):string{
        $pattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
        $isValid = preg_match($pattern, $email);

        if(!$isValid){
            throw new Exception("Email Inválido");
        }

        return $email;
    }
}