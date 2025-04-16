<?php

namespace App\Utils;

use Exception;

class ValidatorProducts
{
    public static function validate(array $data)
    {
        $errors = [];

        foreach ($data as $index => $value) {
            if (is_string($value) && trim($value) === "") {
                $errors[] = "O campo $index deve ser preenchido";
                break;
            }

            if($index == 'products'){
                foreach($value as $i_product => $val_product)
                {
                    if(trim($val_product) === ''){
                        $errors[] = "O campo [$i_product] deve ser preenchido em produtos";
                    }
                }
            }
        }

    }
}
