<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Media;
use App\Utils\Validator;
use Exception;
use PDOException;

require_once("./config.php");

class MediaService{
    public static function createFolder(array $data){

        $pdo = Media::getConnectionDatabase();

        try{
            $pdo->beginTransaction();

            $fields = Validator::validate([
                "folder_name" => strtolower($data['folder_name']) ?? '',
                "parent_id" => $data['parent_id'] ?? ''
            ]);
    
            $nameFolder = Media::getFolderParent($data['parent_id']);
            $path = PATH.$nameFolder;

            $folderId = Media::createFolder($fields);

            if(!$folderId){
                throw new Exception("Não foi possível criar a pasta no banco de dados.");
            }
    
            if(!mkdir($path, 0777, true) && !is_dir($path)){
                throw new Exception("Não foi possível criar a pasta. Tente novamente mais tarde.");
            }
    
            $pdo->commit();

            return "Pasta criada com sucesso!";
        }catch(PDOException $e){
            $pdo->rollBack();
            return ['error' => DatabaseErrorHelpers::error($e)];
        }catch(Exception $e){
            $pdo->rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public static function getAllFolders()
    {

    }

    public static function getAllFiles()
    {

    }

    public static function moveFolder(array $data)
    {

    }

    public static function moveFolderToTrash(array $data)
    {

    }

    public static function moveFileToTrash(array $data)
    {

    }

    public static function restoreFolder(array $data)
    {

    }

    public static function restoreFile(array $data)
    {

    }

    public static function createFile(array $data)
    {

    }

    public static function deleteFolder(array $data)
    {
        
    }

    public static function deleteFile(array $data)
    {

    }

}