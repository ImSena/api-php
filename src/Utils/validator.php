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

    public static function validateLegalPerson(array $fields)
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

        $cnpj = self::validateCNPJ($fields['cnpj']);

        if(!$cnpj){
            throw new Exception("O CNPJ dever ser válido.");
        }

        $corporate_name = self::validateName($fields['corporate_name'], 100);

        if(!$corporate_name){
            throw new Exception("A razão social deve ser válida.");
        }

        $trade_name = self::validateName($fields['trade_name'], 100);

        if(!$trade_name){
            throw new Exception("Nome fantasia deve ser válido");
        }

        $state_registration = self::validateName($fields['state_registration'], 20);

        if(!$state_registration){
            throw new Exception("A inscrição estadual deve ser válida");
        }

        return $fields;
    }

    private static function validateCNPJ($cnpj): bool {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    
        if (strlen($cnpj) != 14) {
            return false;
        }
    
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        $sum = 0;
        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $firstVerifier = ($remainder < 2) ? 0 : 11 - $remainder;
    
        if ($cnpj[12] != $firstVerifier) {
            return false;
        }

        $sum = 0;
        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $secondVerifier = ($remainder < 2) ? 0 : 11 - $remainder;
    
        if ($cnpj[13] != $secondVerifier) {
            return false;
        }
    
        return true;
    }
    
    private static function validateName(string $name, int $max){
        if(!is_string($name)){
            return false;
        }

        if(strlen($name) > $max){
            return false;
        }

        return true;
    }
}