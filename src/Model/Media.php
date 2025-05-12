<?php

namespace App\Model;

use App\Model\Base\BaseModel;
use Exception;
use PDO;

class Media extends BaseModel
{

    //para poder usar no service para begintransaction
    // public function getConnectionDatabase()
    // {
    //     return self::getConnection();
    // }
    //criar o folder uploads caso não exista
    private function ensureRootFolderExists(): void
    {
        $sql = "SELECT id_folder FROM folders WHERE folder_name = 'uploads' AND parent_id IS NULL";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();

        if (!$result) {
            $sql = "INSERT INTO folders (folder_name, parent_id) VALUES ('uploads', NULL)";
            $this->pdo->exec($sql);
        }
    }
    //criar folder
    public function createFolder(array $data, PDO $pdo): bool
    {
        $this->ensureRootFolderExists();

        $sql = "INSERT INTO folders (folder_name, parent_id) VALUES (:folder_name, :parent_id)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":folder_name", $data['folder_name'], PDO::PARAM_STR);

        $stmt->bindParam(":parent_id", $data['parent_id'], PDO::PARAM_INT);

        $stmt->execute();

        return !empty($pdo->lastInsertId());
    }
    public function getContentsInFolder(array $data): array
    {

        $sql = "SELECT 
                f.id_folder AS id, 
                f.folder_name AS name, 
                'folder' AS type, 
                NULL AS file_type
            FROM folders f
            WHERE 
                (:parent_id = 'UPLOADS' AND f.parent_id IS NULL) 
                OR (f.parent_id = :parent_id AND f.is_trash = :is_trash)

            UNION ALL

            SELECT 
                m.id_media AS id, 
                m.alias AS name, 
                'file' AS type, 
                m.file_type
            FROM media m
            WHERE 
                (:parent_id = 'UPLOADS' AND m.id_folder IN (SELECT id_folder FROM folders WHERE parent_id IS NULL)) 
                OR (m.id_folder = :parent_id AND m.is_trash = :is_trash);";

        $stmt = $this->pdo->prepare($sql);

        if ($data['parent_id'] !== 'UPLOADS') {
            $stmt->bindParam(":parent_id", $data['parent_id'], PDO::PARAM_INT);
        }

        $stmt->bindParam(":is_trash", $data['is_trash'], PDO::PARAM_BOOL);

        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as &$row) {
            if ($row['type'] === 'folder') {
                unset($row['file_type']);
                unset($row['file_path']);
            }
        }

        return $results;
    }
    public function editFolder(array $data, PDO $pdo): bool
    {
        $sql = "UPDATE folders SET folder_name = :folder_name, updated_at = :updated_at WHERE id_folder = :id_folder";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":folder_name", $data['folder_name'], PDO::PARAM_STR);
        $stmt->bindParam(":id_folder", $data['id_folder'], PDO::PARAM_INT);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
    public function moveFolder(array $data, $pdo): bool
    {
        $sql = "UPDATE folders SET parent_id = :parent_id, updated_at = :updated_at WHERE id_folder = :id_folder";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":parent_id", $data['parent_id'], PDO::PARAM_INT);
        $stmt->bindParam(":id_folder", $data['id_folder'], PDO::PARAM_INT);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
    private function setFolderTrashStatus(int $id_folder, bool $is_trash): bool
    {

        $sql = "UPDATE folders SET is_trash = :is_trash, updated_at = :updated_at WHERE id_folder = :id_folder";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":is_trash", $is_trash, PDO::PARAM_BOOL);
        $stmt->bindParam(":id_folder", $id_folder, PDO::PARAM_INT);
        $stmt->bindParam(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
        $stmt->execute();

        self::updateSubfoldersAndFiles($this->pdo, $id_folder, $is_trash);

        return $stmt->rowCount() > 0;
    }
    private function updateSubfoldersAndFiles(PDO $pdo, int $id_folder, bool $is_trash): void
    {

        $sql = "UPDATE folders SET is_trash = :is_trash, updated_at = :updated_at WHERE id_folder = :id_folder";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id_folder", $id_folder, PDO::PARAM_INT);
        $stmt->bindParam(":is_trash", $is_trash, PDO::PARAM_BOOL);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
        $stmt->execute();

        $sql = "UPDATE media SET is_trash = :is_trash, updated_at = :updated_at WHERE id_folder = :id_folder";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id_folder", $id_folder, PDO::PARAM_INT);
        $stmt->bindParam(":is_trash", $is_trash, PDO::PARAM_BOOL);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);
        $stmt->execute();

        $parent_id = self::getSubFolder($id_folder);

        if ($parent_id) {
            self::updateSubfoldersAndFiles($pdo, $parent_id['id_folder'], $is_trash);
        }
    }
    private function getSubFolder($id_folder)
    {
        $sql = "SELECT id_folder FROM folders WHERE parent_id = :id_folder";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id_folder", $id_folder, PDO::PARAM_INT);
        $stmt->execute();
        $parent_id = $stmt->fetch();
        return $parent_id ?? null;
    }
    public function moveFolderToTrash(array $data): bool
    {
        return $this->setFolderTrashStatus($data['id_folder'], true);
    }
    public function restoreFolder(array $data): bool
    {
        return $this->setFolderTrashStatus($data['id_folder'], false);
    }
    public function deleteFolder(array $data, $pdo): bool
    {
        $sql = "DELETE FROM folders WHERE id_folder = :id_folder AND is_trash = TRUE";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_folder", $data['id_folder'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    //pegar o caminho completo até as subpasta
    public function getFullFolderPath(int $parent_id): string
    {
        $path = '';

        $this->checkUploads($this->pdo);

        while ($parent_id !== null) {
            $sql = "SELECT folder_name, parent_id FROM folders WHERE id_folder = :parent_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":parent_id", $parent_id, PDO::PARAM_INT);
            $stmt->execute();
            $folder = $stmt->fetch();

            if (!$folder) {
                throw new Exception("Pasta pai não encontrada");
            }

            $path = $folder['folder_name'] . '/' . $path;
            $parent_id = $folder['parent_id'];
        }

        // Caminho final da pasta
        $fullPath = PATH . '/' . trim($path, '/');

        // Criar a hierarquia de diretórios caso não exista
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        return '/' . trim($path, '/');
    }

    private function checkUploads($pdo){
        $sqlCheck = "SELECT id_folder FROM folders WHERE folder_name = 'uploads' AND parent_id IS NULL";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute();
        $uploadsFolder = $stmtCheck->fetch();

        if (!$uploadsFolder) {
            $sqlInsert = "INSERT INTO folders (folder_name, parent_id) VALUES ('uploads', NULL)";
            $pdo->prepare($sqlInsert)->execute();
        }

        if (!is_dir(PATH . '/uploads')) {
            mkdir(PATH . '/uploads', 0777, true);
        }
    }

    // public function getFullFolderPath(int $parent_id): string
    // {
    //     $folderCache = [];  // Cache em memória para pastas

    //     if (isset($folderCache[$parent_id])) {
    //         return $folderCache[$parent_id];
    //     }

    //     $pdo = $this->getPdo();
    //     $path = '';

    //     // Consultar todos os pais de uma vez
    //     $sql = "WITH RECURSIVE folder_hierarchy AS (
    //             SELECT id_folder, folder_name, parent_id
    //             FROM folders
    //             WHERE id_folder = :parent_id
    //         UNION ALL
    //             SELECT f.id_folder, f.folder_name, f.parent_id
    //             FROM folders f
    //             JOIN folder_hierarchy fh ON f.id_folder = fh.parent_id
    //         )
    //         SELECT folder_name, parent_id
    //         FROM folder_hierarchy
    //         ORDER BY parent_id DESC";  // Ordenar para construir o caminho na direção correta

    //     $stmt = $pdo->prepare($sql);
    //     $stmt->bindParam(":parent_id", $parent_id, PDO::PARAM_INT);
    //     $stmt->execute();

    //     // Construir o caminho
    //     $result = $stmt->fetchAll();
    //     foreach ($result as $folder) {
    //         $path = $folder['folder_name'] . '/' . $path;
    //     }

    //     $folderCache[$parent_id] = '/' . rtrim($path, '/');
    //     return $folderCache[$parent_id];
    // }


    // public function getPathToFolder(int $id_folder, bool $old = true, bool $delete = false): string
    // {
    //     $folderCache = [];

        
    //     if (isset($folderCache[$id_folder])) {
    //         return $folderCache[$id_folder];
    //     }
        
    //     $pdo = $this->getPdo();
    //     $this->checkUploads($pdo);
    //     $sql = "SELECT parent_id, folder_name FROM folders WHERE id_folder = :id_folder";

    //     $stmt = $pdo->prepare($sql);
    //     $stmt->bindParam(":id_folder", $id_folder, PDO::PARAM_INT);
    //     $stmt->execute();

    //     $result = $stmt->fetch();
    //     if (!$result) {
    //         throw new Exception("Pasta não encontrada");
    //     }

    //     $path = "";
    //     if (empty($result['parent_id'])) {
    //         $path = "/uploads";
    //     } else {
    //         if ($old) {
    //             $path =  self::getFullFolderPath($result['parent_id']);
    //         } else {
    //             $path = self::getFullFolderPath($result['parent_id']) . '/' . $result['folder_name'];
    //         }
    //     }

    //     // Armazenar o caminho da pasta no cache
    //     $folderCache[$id_folder] = $path;

    //     return $path;
    // }

    public function getPathToFolder(int $id_folder, bool $old = true, bool $delete = false): string
    {

        $sql = "SELECT parent_id, folder_name FROM folders WHERE id_folder = :id_folder";

        $stmt = $this->pdo->prepare($sql);

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
                $path =  $this->getFullFolderPath($result['parent_id']);
            } else {
                $path = $this->getFullFolderPath($result['parent_id']) . '/' . $result['folder_name'];
            }
        }
        return $path;
    }
    //Files
    public function createFiles(int $id_folder, array $files, PDO $pdo): bool
    {
        $sql = "INSERT INTO media (file_name, alias, file_type, file_size, id_folder) 
                VALUES (:file_name, :alias, :file_type, :file_size, :id_folder)";
        $stmt = $pdo->prepare($sql);

        foreach ($files as $file) {
            $stmt->bindValue(":file_name", $file['name_date'], PDO::PARAM_STR);
            $stmt->bindValue(":alias", $file['unique_name'], PDO::PARAM_STR);
            $stmt->bindValue(":file_type", $file['type'], PDO::PARAM_STR);
            $stmt->bindValue(":file_size", $file['size'], PDO::PARAM_INT);
            $stmt->bindValue(":id_folder", $id_folder, PDO::PARAM_INT);

            $stmt->execute();
        }

        return $stmt->rowCount() > 0;
    }

    public function editFile(array $data): bool
    {

        $sql = "UPDATE media SET alias = :file_name, updated_at = :updated_at WHERE id_media = :id_media";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":file_name", $data['file_name'], PDO::PARAM_STR);
        $stmt->bindParam(":id_media", $data['id_file'], PDO::PARAM_INT);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getExtensionAndName(int $id_file)
    {

        $sql = "SELECT file_type, alias FROM media WHERE id_media = :id_media";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":id_media", $id_file, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function fileExists(string $alias): bool
    {


        $sql = "SELECT COUNT(*) FROM media WHERE alias = :alias";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":alias", $alias, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }


    // public function getPathToFile(array $data): string
    // {
    //     $folderCache = [];  // Variável estática para armazenar caminhos de pastas em cache

    //     // Verificar cache para o caminho do arquivo
    //     $pdo = $this->getPdo();

    //     // Recuperar as informações do arquivo
    //     $sql = "SELECT m.file_name, f.parent_id, f.id_folder, m.file_type FROM media m
    //         JOIN FOLDERS f ON m.id_folder = f.id_folder
    //         WHERE m.id_media = :id_media";

    //     $stmt = $pdo->prepare($sql);
    //     $stmt->bindParam(":id_media", $data['id_media'], PDO::PARAM_INT);
    //     $stmt->execute();

    //     $result = $stmt->fetch();
    //     // if (!$result) {
    //     //     throw new Exception("Arquivo não encontrado");
    //     // }

    //     // Verificar se o caminho da pasta está no cache
    //     if (!isset($folderCache[$result['id_folder']])) {
    //         $folderPath = $this->getPathToFolder($result['id_folder'], false);
    //         $folderCache[$result['id_folder']] = $folderPath;
    //     } else {
    //         $folderPath = $folderCache[$result['id_folder']];
    //     }

    //     $filePath = $folderPath . '/' . $result['file_name'];
    //     return $filePath;
    // }


    public function getPathToFile(array $data): string
    {

        $sql = "SELECT m.file_name, f.parent_id, f.id_folder, m.file_type FROM media m
                JOIN folders f ON m.id_folder = f.id_folder
                WHERE m.id_media = :id_media";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id_media", $data['id_media'], PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        $folderPath = $this->getPathToFolder($result['id_folder'], false);
        $filePath = $folderPath . '/' . $result['file_name'];

        return $filePath;
    }
    public function moveFile(array $data, PDO $pdo): bool
    {
        $sql = "UPDATE media SET id_folder = :id_folder, updated_at = :updated_at WHERE id_media = :id_media";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_folder", $data['id_folder'], PDO::PARAM_INT);
        $stmt->bindParam(":id_media", $data['id_media'], PDO::PARAM_INT);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
    private function setStatusTrashFile(int $id_media, bool $is_trash)
    {

        $sql = "UPDATE media SET is_trash = :is_trash, updated_at = :updated_at WHERE id_media = :id_media";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(":is_trash", $is_trash, PDO::PARAM_BOOL);
        $stmt->bindParam(":id_media", $id_media, PDO::PARAM_INT);
        $stmt->bindValue(":updated_at", $this->currentDatetime, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
    public function moveFileToTrash(array $data): bool
    {
        return $this->setStatusTrashFile($data['id_media'], true);
    }

    public function restoreFile(array $data): bool
    {
        return $this->setStatusTrashFile($data['id_media'], false);
    }

    public function deleteFile(array $data, PDO $pdo): bool
    {
        $sql = "DELETE FROM media WHERE id_media = :id_media AND is_trash = TRUE";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":id_media", $data['id_media'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getFile(int $id){
        $sql = "SELECT file_name, alias, file_type, file_size FROM media WHERE is_trash < 1 AND id_media = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}
