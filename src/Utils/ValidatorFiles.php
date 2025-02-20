<?php

namespace App\Utils;

use Exception;

class ValidatorFiles
{
    public static function validate(array $files):array{

        $errors = [];

        $allowedTypes = ['image/jpg', 'image/png', 'image/jpeg'];
        $allowewdExtensions = ['jpg', 'jpeg', 'png'];
        $maxSize = 100 * 1024; //100kb

        if(empty($files)){
            $errors[] = "Nenhum arquivo foi enviado";
        }
        
        foreach($files as $index => $value){
            
            foreach($value as $file){
                if(!in_array($file['type'], $allowedTypes)){
                    $errors[] = "Tipo de arquivo não permitido para o arquivo {$file['name']}";
                    break;
                }
    
                if($file['size'] > $maxSize){
                    $errors[] = "O arquivo {$file['name']} é muito grande. Tamanho máximo é de 100kb";
                    break;
                }
    
                if($file['error'] !== UPLOAD_ERR_OK){
                    $errors[] = "Erro no upload do arquivo {$file['name']}.";
                    break;
                }
    
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
                if(!in_array($extension, $allowewdExtensions)){
                    $errors[] = "A extensão do arquivo {$file['name']} não é permitida";
                    break;
                }
    
                if(getimagesize($file['tmp_name']) === false){
                    $errors[] = "O arquivo {$file['name']} não é uma imagem válida.";
                }
    
                list($width, $height) = getimagesize($file['tmp_name']);
    
                if($width < 100 || $height < 100){
                    $errors[] = "A imagem {$file['name']} deve ter pelo menos 100x100 pixels.";
                    break;
                }
            }
        }

        if(!empty($errors)){
            throw new Exception("Erros de validação: ". implode(', ', $errors));
        }

        return $files;
    }
}