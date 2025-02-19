<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Media;
use App\Utils\Validator;
use Exception;
use PDOException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

require_once("./config.php");

class MediaService
{
    public static function getAllInFolder(array $data): array | string
    {
        try {

            $fields = Validator::validate([
                "parent_id" => $data['parent_id'] ?? 1,
                "is_trash" => $data['is_trash'] ?? false
            ]);

            $Folders = Media::getContentsInFolder($fields);

            return $Folders;
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    public static function createFolder(array $data): array | string
    {
        $pdo = Media::getConnectionDatabase();

        try {
            $pdo->beginTransaction();

            $fields = Validator::validate([
                "folder_name" => strtolower($data['folder_name']) ?? '',
                "parent_id" => $data['parent_id'] ?? '',
            ]);

            $folderId = Media::createFolder($fields, $pdo);

            if (!$folderId) {
                throw new Exception("Não foi possível criar a pasta no banco de dados.");
            }

            $path = PATH . Media::getFullFolderPath($data['parent_id']) . '/' . $data['folder_name'];

            if (!is_dir($path) && !mkdir($path, 0777, true)) {
                throw new Exception("Erro ao criar a pasta no servidor.");
            }

            $pdo->commit();

            return "Pasta criada com sucesso!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['error' => $e->getMessage()];
        }
    }
    public static function editFolder(array $data): array | string
    {

        $pdo = Media::getConnectionDatabase();
        try {

            $pdo->beginTransaction();

            $fields = Validator::validate([
                "id_folder" => $data['id_folder'] ?? '',
                "folder_name" => $data['folder_name'] ?? '',
            ]);

            $fields['id_folder'] = intval($fields['id_folder']);

            if($fields['id_folder'] == 1 || $fields['id_folder'] < 1){
                throw new Exception("Não foi possível editar o nome da pasta.");
            }
            $newPath = PATH . Media::getPathToFolder($fields['id_folder']) . '/' . $fields['folder_name'];
            
            $oldPath = PATH . Media::getPathToFolder($fields['id_folder'], false);

            $Folder = Media::editFolder($fields, $pdo);

            if (!$Folder) {
                throw new Exception("Não foi possível editar a pasta.");
            }

            if (!rename($oldPath, $newPath)) {
                throw new Exception("Erro ao renomear a pasta no servidor.");
            }

            $pdo->commit();
            return "Pasta editada com sucesso!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['error' => $e->getMessage()];
        }
    }
    //terminar lógica para mudar id dos arquivos também
    public static function moveFolder(array $data): array | string
    {
        $pdo = Media::getConnectionDatabase();

        try {
            $pdo->beginTransaction();

            $fields = Validator::validate([
                "id_folder" => $data['id_folder'] ?? '',
                "id_new_folder" => $data['id_new_folder'] ?? '',
            ]);

            $fields = array_map('intval', $fields);

            if($fields['id_folder'] == 1 || $fields['id_folder'] < 1){
                throw new Exception("Não foi possível mover a pasta.");
            }

            $Folder = Media::moveFolder($fields, $pdo);

            if (!$Folder) {
                throw new Exception("Não foi possível mover a pasta.");
            }

            $oldPath = PATH . Media::getPathToFolder($fields['id_folder'], true);
            $newPath = PATH . Media::getPathToFolder($fields['id_new_folder']);

            if (!is_dir($newPath)) {
                if (!mkdir($newPath, 0777, true)) {
                    throw new Exception("Erro ao criar a nova pasta no servidor.");
                }
            }

            $files = scandir($oldPath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    if (!rename($oldPath . '/' . $file, $newPath . '/' . $file)) {
                        throw new Exception("Erro ao mover o arquivo: " . $file);
                    }
                }
            }

            // if (!rmdir($oldPath)) {
            //     throw new Exception("Erro ao remover a pasta antiga no servidor.");
            // }

            $pdo->commit();
            return "Pasta movida com sucesso!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['error' => $e->getMessage()];
        }
    }
    public static function moveFolderToTrash(array $data): array | string
    {
        try {

            $fields = Validator::validate([
                "id_folder" => $data['id_folder'] ?? '',
            ]);

            $fields['id_folder'] = intval($fields['id_folder']);

            if($fields['id_folder'] == 1 || $fields['id_folder'] < 1){
                throw new Exception("Não foi possível mover a pasta para a lixeira.");
            }

            $Folder = Media::moveFolderToTrash($fields);

            if (!$Folder) {
                throw new Exception("Não foi possível mover a pasta para a lixeira.");
            }

            return "Pasta movida para a lixeira com sucesso!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    public static function restoreFolder(array $data): array | string
    {
        try {

            $fields = Validator::validate([
                "id_folder" => $data['id_folder'] ?? '',
            ]);

            $fields['id_folder'] = intval($fields['id_folder']);

            if($fields['id_folder'] == 1 || $fields['id_folder'] < 1){
                throw new Exception("Não foi possível restaurar a pasta.");
            }

            $Folder = Media::restoreFolder($fields);

            if (!$Folder) {
                throw new Exception("Não foi possível restaurar a pasta.");
            }

            return "Pasta restaurada com sucesso!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    public static function deleteFolder(array $data): array | string
    {
        $pdo = Media::getConnectionDatabase();

        try {
            $pdo->beginTransaction();

            $fields = Validator::validate([
                "id_folder" => $data['id_folder'] ?? '',
            ]);

            $fields['id_folder'] = intval($fields['id_folder']);

            if($fields['id_folder'] == 1 || $fields['id_folder'] < 1){
                throw new Exception("Não foi possível deletar a pasta.");
            }

            $Folder = Media::deleteFolder($fields, $pdo);
            
            if (!$Folder) {
                throw new Exception("Não foi possível deletar a pasta.");
            }
            
            $folderPath = PATH . Media::getPathToFolder($fields['id_folder'], false);

            if($folderPath == "/uploads"){
                throw new Exception("Não foi possível deletar a pastaaa.");
            }

            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                if (!$todo($fileinfo->getRealPath())) {
                    throw new Exception("Erro ao deletar o arquivo ou pasta: " . $fileinfo->getRealPath());
                }
            }

            if (!rmdir($folderPath)) {
                throw new Exception("Erro ao remover a pasta no servidor.");
            }

            $pdo->commit();

            return "Pasta deletada com sucesso!";
        } catch (PDOException $e) {
            $pdo->rollback();
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            $pdo->rollback();
            return ['error' => $e->getMessage()];
        }
    }

    // Files
    public static function uploadFile(array $data): array | string
    {
        $pdo = Media::getConnectionDatabase();
        try {
            $fields = Validator::validate([
                "file_name" => strtolower($data['file_name']) ?? '',
                "parent_id" => $data['parent_id'] ?? '',
                "file" => $data['file'] ?? '',
            ]);

            $File = Media::createFiles($fields);

            if (!$File) {
                throw new Exception("Não foi possível criar o arquivo no banco de dados.");
            }

            $path = PATH . Media::getFullFolderPath($data['parent_id']) . '/' . $data['file_name'];

            if (!is_dir($path) && !mkdir($path, 0777, true)) {
                throw new Exception("Erro ao criar o arquivo no servidor.");
            }

            $pdo->commit();

            return "Arquivo criado com sucesso!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public static function moveFile(array $data): array | string
    {
        try {

            $fields = Validator::validate([
                "id_file" => $data['id_file'] ?? '',
                "parent_id" => $data['parent_id'] ?? '',
            ]);

            $File = Media::moveFile($fields);

            if (!$File) {
                throw new Exception("Não foi possível mover o arquivo.");
            }

            return "Arquivo movido com sucesso!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function moveFileToTrash(array $data): array | string
    {
        try {

            $fields = Validator::validate([
                "id_file" => $data['id_file'] ?? '',
            ]);

            $File = Media::moveFileToTrash($fields);

            if (!$File) {
                throw new Exception("Não foi possível mover o arquivo para a lixeira.");
            }

            return "Arquivo movido para a lixeira com sucesso!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function restoreFile(array $data): array | string
    {
        try {

            $fields = Validator::validate([
                "id_file" => $data['id_file'] ?? '',
            ]);

            $File = Media::restoreFile($fields);

            if (!$File) {
                throw new Exception("Não foi possível restaurar o arquivo.");
            }

            return "Arquivo restaurado com sucesso!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function deleteFile(array $data): array | string
    {
        try {

            $fields = Validator::validate([
                "id_file" => $data['id_file'] ?? '',
            ]);

            $File = Media::deleteFile($fields);

            if (!$File) {
                throw new Exception("Não foi possível deletar o arquivo.");
            }

            return "Arquivo deletado com sucesso!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function editFile(array $data): array | string
    {
        try {

            $fields = Validator::validate([
                "id_file" => $data['id_file'] ?? '',
                "file_name" => $data['file_name'] ?? '',
            ]);

            $File = Media::editFile($fields);

            if (!$File) {
                throw new Exception("Não foi possível editar o arquivo.");
            }

            return "Arquivo editado com sucesso!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
