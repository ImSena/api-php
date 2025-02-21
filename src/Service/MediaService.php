<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Media;
use App\Utils\Validator;
use App\Utils\ValidatorFiles;
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

            if ($fields['id_folder'] == 1 || $fields['id_folder'] < 1) {
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

            if ($fields['id_folder'] == 1 || $fields['id_folder'] < 1) {
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

            self::moveAll($oldPath, $newPath);

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
    private static function moveAll(string $source, string $destination): void
    {
        $files = scandir($source);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $srcPath = $source . '/' . $file;
                $destPath = $destination . '/' . $file;

                if (is_dir($srcPath)) {
                    if (!is_dir($destPath)) {
                        mkdir($destPath, 0777, true);
                    }
                    self::moveAll($srcPath, $destPath);
                } else {
                    // Mover arquivos normais
                    if (!rename($srcPath, $destPath)) {
                        throw new Exception("Erro ao mover o arquivo: " . $file);
                    }
                }
            }
        }
    }
    public static function moveFolderToTrash(array $data): array | string
    {
        try {

            $fields = Validator::validate([
                "id_folder" => $data['id_folder'] ?? '',
            ]);

            $fields['id_folder'] = intval($fields['id_folder']);

            if ($fields['id_folder'] == 1 || $fields['id_folder'] < 1) {
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

            if ($fields['id_folder'] == 1 || $fields['id_folder'] < 1) {
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

            if ($fields['id_folder'] == 1 || $fields['id_folder'] < 1) {
                throw new Exception("Não foi possível deletar a pasta.");
            }

            $Folder = Media::deleteFolder($fields, $pdo);

            if (!$Folder) {
                throw new Exception("Não foi possível deletar a pasta.");
            }

            $folderPath = PATH . Media::getPathToFolder($fields['id_folder'], false);

            if ($folderPath == "/uploads") {
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
    public static function uploadFile(array $data, array $files): array | string
    {
        $pdo = Media::getConnectionDatabase();
        try {
            $pdo->beginTransaction();

            $fields = Validator::validate([
                "id_folder" => $data['id_folder'] ?? '',
            ]);

            $files = ValidatorFiles::validate(["files" => $files ?? '']);

            $files['files'] = self::reformatFilesArray($files['files']);

            $path_folder = PATH . Media::getPathToFolder($fields['id_folder'], false);

            if (!is_dir($path_folder)) {
                throw new Exception("Pasta não existe no servidor.");
            }

            $existingFiles = [];

            foreach ($files['files'] as &$file) {
                usleep(1);
                $file['unique_name'] = self::generateUniqueFilename($path_folder, $file['name'], $existingFiles);
                $file['name_date'] = round(microtime(true) * 1000) . rand(1000, 9999);
                $existingFiles[] = $file['unique_name'];
            }

            $File = Media::createFiles($fields['id_folder'], $files['files'], $pdo);

            if (!$File) {
                throw new Exception("Não foi possível criar o arquivo no banco de dados.");
            }


            foreach ($files['files'] as $key => $filez) {
                if (!file_exists($filez['tmp_name'])) {
                    throw new Exception("O arquivo temporário não existe: " . $filez['tmp_name']);
                }

                $extension = pathinfo($filez['name'], PATHINFO_EXTENSION);
                $targetPath = $path_folder . DIRECTORY_SEPARATOR . $filez['name_date'] . '.' . $extension;

                if (!move_uploaded_file($filez['tmp_name'], $targetPath)) {
                    throw new Exception("Erro ao mover o arquivo para o servidor: " . $filez['tmp_name']);
                }

                if (!file_exists($targetPath)) {
                    throw new Exception("O arquivo não foi carregado corretamente.");
                }
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
    private static function generateUniqueFilename(string $directory, string $filename, array $existingFiles): string
    {
        $fileInfo = pathinfo($filename);
        $baseName = preg_replace("/[^a-zA-Z0-9-_]/", "", $fileInfo['filename']); // Remove caracteres inválidos
        $extension = isset($fileInfo['extension']) ? '.' . $fileInfo['extension'] : '';

        $newFilename = $baseName . $extension;

        if (!file_exists($directory . DIRECTORY_SEPARATOR . $newFilename) && !in_array($newFilename, $existingFiles)) {
            return $newFilename;
        }

        $counter = 1;
        while (file_exists($directory . DIRECTORY_SEPARATOR . $newFilename) || in_array($newFilename, $existingFiles)) {
            $newFilename = $baseName . "_" . $counter . $extension;
            $counter++;
        }

        return $newFilename;
    }
    private static function reformatFilesArray(array $files): array
    {
        $reformatted = [];
        foreach ($files as $file) {
            if (is_array($file['name'])) {
                foreach ($file['name'] as $index => $name) {
                    $reformatted[] = [
                        'name' => $name,
                        'type' => $file['type'][$index],
                        'tmp_name' => $file['tmp_name'][$index],
                        'error' => $file['error'][$index],
                        'size' => $file['size'][$index]
                    ];
                }
            } else {
                $reformatted[] = $file;
            }
        }
        return $reformatted;
    }
    public static function editFile(array $data): array | string
    {
        try {
            $fields = Validator::validate([
                "id_file" => $data['id_file'] ?? '',
                "file_name" => $data['file_name'] ?? '',
            ]);

            $info_file = Media::getExtensionAndName($fields['id_file']);

            if (!$info_file) {
                throw new Exception("Não foi possível editar o arquivo");
            }

            $extension = self::getExtension($info_file['file_type']);
            $baseName = pathinfo($fields['file_name'], PATHINFO_FILENAME);
            $newName = $baseName;

            if (preg_match('/\((\d+)\)$/', $baseName, $matches)) {
                $baseName = preg_replace('/\(\d+\)$/', '', $baseName);
                $counter = (int) $matches[1];
            } else {
                $counter = 1;
            }

            while (Media::fileExists($newName . '.' . $extension)) {
                $newName = $baseName . "(" . $counter . ")";
                $counter++;
            }

            $fields['file_name'] = $newName . '.' . $extension;

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
    private static function getExtension($file_type):string
    {
        $allowedTypes = [
            'image/jpg' => 'jpg',
            'image/jpeg' => 'jpeg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'video/mp4' => 'mp4',
            'video/quicktime' => 'mov',
            'video/x-msvideo' => 'avi',
            'video/x-matroska' => 'mkv',
            'video/webm' => 'webm',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'text/csv' => 'csv',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'audio/mpeg' => 'mp3',
            'audio/wav' => 'wav',
            'audio/ogg' => 'ogg',
            'audio/aac' => 'aac',
        ];

        if(!isset($allowedTypes[$file_type])){
            throw new Exception("Extensão inválida: '{$file_type}'. Não foi possível atualizar o nome do arquivo.");
        }

        return $allowedTypes[$file_type];

    }
    public static function moveFile(array $data): array | string
    {
        
        $pdo = Media::getConnectionDatabase();
        try {

            $pdo->beginTransaction();

            $fields = Validator::validate([
                "id_folder" => $data['id_folder'] ?? '',
                "id_media" => $data['id_media'] ?? '',
            ]);
            
            $info_file = Media::getExtensionAndName($fields['id_media']);

            if (!$info_file) {
                throw new Exception("Não foi possível excluir o arquivo");
            }

            $File = Media::moveFile($fields, $pdo);

            if (!$File) {
                throw new Exception("Não foi possível mover o arquivo.");
            }
            
            $extension = self::getExtension($info_file['file_type']);
            $filePath = PATH . Media::getPathToFile($fields).".".$extension;
            $newPath = PATH . Media::getPathToFolder($fields['id_folder'], false) . '/' . basename($filePath) ;
            
            if (!rename($filePath, $newPath)) {
                throw new Exception("Erro ao mover o arquivo no servidor.");
            }
            
            $pdo->commit();
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
                "id_media" => $data['id_media'] ?? '',
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
                "id_media" => $data['id_media'] ?? '',
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
        $pdo = Media::getConnectionDatabase();

        try {
            $pdo->beginTransaction();

            $fields = Validator::validate([
                "id_media" => $data['id_media'] ?? '',
            ]);

            $info_file = Media::getExtensionAndName($fields['id_media']);

            if (!$info_file) {
                throw new Exception("Não foi possível excluir o arquivo");
            }

            $extension = self::getExtension($info_file['file_type']);

            $File = Media::deleteFile($fields, $pdo);

            if (!$File) {
                throw new Exception("Não foi possível deletar o arquivo.");
            }

            $filePath = PATH . Media::getPathToFile($data).".".$extension;

            if (!file_exists($filePath)) {
                throw new Exception("O arquivo não existe: " . $filePath);
            }
           
            if (!unlink($filePath)) {
                throw new Exception("Erro ao deletar o arquivo no servidor.");
            }
            
            $pdo->commit();

            return "Arquivo deletado com sucesso!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['error' => $e->getMessage()];
        }
    }
}
