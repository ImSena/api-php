<?php

namespace App\Model;

use Exception;
use PDO;

class Media extends Database
{

    //para poder usar no service para begintransaction
    public static function getConnectionDatabase()
    {
        return self::getConnection();
    }
    //criar o folder uploads caso não exista
    private static function ensureRootFolderExists(): void
    {
        $pdo = self::getConnection();
        $sql = "SELECT id_folder FROM FOLDERS WHERE folder_name = 'uploads' AND parent_id IS NULL";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();

        if (!$result) {
            $sql = "INSERT INTO FOLDERS (folder_name, parent_id) VALUES ('uploads', NULL)";
            $pdo->exec($sql);
        }
    }
    //criar folder
    public static function createFolder(array $data, PDO $pdo): bool
    {
        self::ensureRootFolderExists();

        $sql = "INSERT INTO FOLDERS (folder_name, parent_id) VALUES (:folder_name, :parent_id)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":folder_name", $data['folder_name'], PDO::PARAM_STR);

        $stmt->bindParam(":parent_id", $data['parent_id'], PDO::PARAM_INT);

        $stmt->execute();

        return !empty($pdo->lastInsertId());
    }
    public static function getContentsInFolder(array $data): array
    {
        $pdo = self::getConnection();

        $sql = "
            SELECT f.id_folder AS id, f.folder_name AS name, 'folder' AS type, NULL AS file_type, NULL AS file_path
            FROM FOLDERS f
            WHERE " . ($data['parent_id'] === 'UPLOADS' ? "f.parent_id IS NULL" : "f.parent_id = :parent_id") . " 
            AND f.is_trash = :is_trash
            UNION ALL
            SELECT m.id_media AS id, m.file_name AS name, 'file' AS type, m.file_type, m.file_path
            FROM MEDIA m
            WHERE " . ($data['parent_id'] === 'UPLOADS' ? "m.id_folder IN (SELECT id_folder FROM FOLDERS WHERE parent_id IS NULL)" : "m.id_folder = :parent_id") . " 
            AND m.is_trash = :is_trash
        ";

        $stmt = $pdo->prepare($sql);

        if ($data['parent_id'] !== 'UPLOADS') {
            $stmt->bindParam(":parent_id", $data['parent_id'], PDO::PARAM_INT);
        }

        $stmt->bindParam(":is_trash", $data['is_trash'], PDO::PARAM_BOOL);

        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Remover 'file_type' e 'file_path' das pastas no PHP
        foreach ($results as &$row) {
            if ($row['type'] === 'folder') {
                unset($row['file_type']);
                unset($row['file_path']);
            }
        }

        return $results;
    }

    public static function editFolder(array $data, PDO $pdo): bool
    {
        $sql = "UPDATE FOLDERS SET folder_name = :folder_name WHERE id_folder = :id_folder";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":folder_name", $data['folder_name'], PDO::PARAM_STR);
        $stmt->bindParam(":id_folder", $data['id_folder'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function moveFolder(array $data, $pdo): bool
    {
        $sql = "UPDATE FOLDERS SET parent_id = :parent_id WHERE id_folder = :id_folder";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":parent_id", $data['parent_id'], PDO::PARAM_INT);
        $stmt->bindParam(":id_folder", $data['id_folder'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    private static function setFolderTrashStatus(int $id_folder, bool $is_trash): bool
    {
        $pdo = self::getConnection();

        $sql = "UPDATE FOLDERS SET is_trash = :is_trash WHERE id_folder = :id_folder";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":is_trash", $is_trash, PDO::PARAM_BOOL);
        $stmt->bindParam(":id_folder", $id_folder, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function moveFolderToTrash(array $data): bool
    {
        return self::setFolderTrashStatus($data['id_folder'], true);
    }

    public static function restoreFolder(array $data): bool
    {
        return self::setFolderTrashStatus($data['id_folder'], false);
    }
    public static function deleteFolder(array $data, $pdo): bool
    {
        $sql = "DELETE FROM FOLDERS WHERE id_folder = :id_folder AND is_trash = TRUE";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_folder", $data['id_folder'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
    //pegar o caminho completo até as subpasta
    public static function getFullFolderPath(int $parent_id): string
    {
        $pdo = self::getConnection();
        $path = '';

        while ($parent_id !== null) {
            $sql = "SELECT folder_name, parent_id FROM FOLDERS WHERE id_folder = :parent_id";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(":parent_id", $parent_id, PDO::PARAM_INT);

            $stmt->execute();

            $folder = $stmt->fetch();

            if (!$folder) {
                throw new Exception("Pasta pai não encontrada");
            }

            $path = $folder['folder_name'] . '/' . $path;
            $parent_id = $folder['parent_id'];
        }

        return '/' . rtrim($path, '/');
    }

    public static function getPathToFolder(int $id_folder, bool $old = true, bool $delete = false): string
    {
        $pdo = self::getConnection();

        $sql = "SELECT parent_id, folder_name FROM FOLDERS WHERE id_folder = :id_folder";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_folder", $id_folder, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetch();

        if (!$result) {
            throw new Exception("Pasta não encontrada");
        }

        $path = "";


        if (empty($result['parent_id'])) {
            $path = "/uploads";
        } else {
            if ($old) {
                $path =  self::getFullFolderPath($result['parent_id']);
            } else {
                $path = self::getFullFolderPath($result['parent_id']) . '/' . $result['folder_name'];
            }
        }
        return $path;
    }

    //Files
    public static function createFiles(array $data): bool
    {
        $pdo = self::getConnection();

        $sql = "INSERT INTO MEDIA (file_name, id_folder) VALUES (:file_name, :id_folder)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":file_name", $data['file_name'], PDO::PARAM_STR);
        $stmt->bindParam(":id_folder", $data['id_folder'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function editFile(array $data): bool
    {
        $pdo = self::getConnection();

        $sql = "UPDATE MEDIA SET file_name = :file_name WHERE id_media = :id_media";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":file_name", $data['file_name'], PDO::PARAM_STR);
        $stmt->bindParam(":id_media", $data['id_media'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function moveFile(array $data): bool
    {
        $pdo = self::getConnection();

        $sql = "UPDATE MEDIA SET id_folder = :id_folder WHERE id_media = :id_media";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_folder", $data['id_folder'], PDO::PARAM_INT);
        $stmt->bindParam(":id_media", $data['id_media'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function moveFileToTrash(array $data): bool
    {
        $pdo = self::getConnection();

        $sql = "UPDATE MEDIA SET is_trash = TRUE WHERE id_media = :id_media";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_media", $data['id_media'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function restoreFile(array $data): bool
    {
        $pdo = self::getConnection();

        $sql = "UPDATE MEDIA SET is_trash = FALSE WHERE id_media = :id_media";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_media", $data['id_media'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function deleteFile(array $data): bool
    {
        $pdo = self::getConnection();

        $sql = "DELETE FROM MEDIA WHERE id_media = :id_media";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_media", $data['id_media'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
