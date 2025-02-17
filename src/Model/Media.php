<?php

namespace App\Model;

use PDO;

class Media extends Database
{

    public static function getConnectionDatabase(){
        return self::getConnection();
    }
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
    public static function createFolder(array $data): bool
    {
        $pdo = self::getConnection();

        self::ensureRootFolderExists();

        $sql = "INSERT INTO FOLDERS (folder_name, parent_id) VALUES (:folder_name, :parent_id)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":folder_name", $data['folder_name'], PDO::PARAM_STR);

        $stmt->bindParam(":parent_id", $data['parent_id'], PDO::PARAM_INT);

        $stmt->execute();

        return !empty($pdo->lastInsertId());
    }
    public static function getFolders(array $data)
    {
        $pdo = self::getConnection();

        $sql = "SELECT * FROM FOLDERS WHERE is_trash = 0 WHERE id_folder = :id_folder";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_folder", $data['id_folder'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getFolderHierarchy(int $parent_id = null): array
    {
        $pdo = self::getConnection();

        $sql = "SELECT * FROM FOLDERS WHERE parent_id " . ($parent_id === null ? "IS NULL" : "= :parent_id") . " AND is_trash = 0";

        $stmt = $pdo->prepare($sql);

        if ($parent_id !== null) {
            $stmt->bindParam(":parent_id", $parent_id, PDO::PARAM_INT);
        }

        $stmt->execute();

        $folders = $stmt->fetchAll();

        foreach ($folders as &$folder) {
            $folder['subfolders'] = self::getFolderHierarchy($folder['id_folder']);
        }

        return $folders;
    }

    public static function getFolderParent(array $data)
    {
        $pdo = self::getConnection();

        $sql = "SELECT folder_name FROM FOLDERS WHERE parent_id = :parent_id";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":parent_id", $data['parent_id'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getFoldersTrash(): array
    {
        $pdo = self::getConnection();

        $sql = "SELECT * FROM FOLDERS WHERE is_trash = 1";

        $stmt = $pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function editFolder(array $data): bool
    {
        $pdo = self::getConnection();

        $sql = "UPDATE FOLDERS SET folder_name = :folder_name WHERE id_folder = :id_folder";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":folder_name", $data['folder_name'], PDO::PARAM_STR);
        $stmt->bindParam(":id_folder", $data['id_folder'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function moveFolder(array $data): bool
    {
        $pdo = self::getConnection();

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
        return self::setFolderTrashStatus($data['id_folder'], 1);
    }

    public static function restoreFolder(array $data): bool
    {
        return self::setFolderTrashStatus($data['id_folder'], 0);
    }
    public static function deleteFolder(array $data): bool
    {
        $pdo = self::getConnection();

        $sql = "DELETE FROM FOLDERS WHERE id_folder = :id_folder";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_folder", $data['id_folder'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function getFilesInFolder(int $id_folder): array
    {
        $pdo = self::getConnection();

        $sql = "SELECT * FROM MEDIA WHERE id_folder = :id_folder AND is_trash = 0";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id_folder", $id_folder, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getFolderHierarchyWithFiles(int $parent_id = null): array
    {
        $pdo = self::getConnection();
    
        $sql = "SELECT * FROM FOLDERS WHERE parent_id " . ($parent_id === null ? "IS NULL" : "= :parent_id") . " AND is_trash = 0";
        $stmt = $pdo->prepare($sql);
    
        if ($parent_id !== null) {
            $stmt->bindParam(":parent_id", $parent_id, PDO::PARAM_INT);
        }
    
        $stmt->execute();
        $folders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($folders as &$folder) {
            $folder['subfolders'] = self::getFolderHierarchyWithFiles($folder['id_folder']);
            $folder['files'] = self::getFilesInFolder($folder['id_folder']);
        }
    
        return $folders;
    }
    
}
