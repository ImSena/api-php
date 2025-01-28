<?php

namespace App\Service;

use Exception;

class FileProductService
{
    public static function upload($files, $id_product){
        $directory = 'uploads/products/'.$id_product;
        //trocar o path server quando for fazer no cliente, tirar o /api-php e também colocar ssl;
        $path_server = 'http://'.$_SERVER['HTTP_HOST'].'/api-php/'.$directory.'/';

        $directory = './'.$directory;

        $errors = [];
        $uploadsFiles = [];
        
        
        try{

            if(!is_dir($directory)){
                if(!mkdir($directory, 0777, true)){
                    throw new Exception("Não foi possível criar o diretório: ".$directory);
                }
            }

            foreach($files as $index => $value){
                
                foreach($value as $file){
                    $newFileName = date('Ymd_His') . '_' . uniqid(). '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
                    
                    if(move_uploaded_file($file['tmp_name'], $directory . '/'. $newFileName)){
                        $uploadsFiles[] = $path_server.$newFileName;
                    }else{
                        $errors[] = $file['name'];
                    }
                }
            }
            if(!empty($errors)){
                $qtd_errors = count($errors) > 1;
                $message = $qtd_errors ? "Não foi possível fazer upload dos arquivos: " : "Não foi possível fazer upload do arquivo: ";
                throw new Exception($message . implode(', ', $errors));
            }

            return $uploadsFiles;
        }catch(Exception $e){
            throw $e;
        }
    }

    public static function deleteServer(int $id)
    {
        $directory = './uploads/products/'.$id;

        if(is_dir($directory)){
            return rmdir($directory);
        }

        return false;
    }
}