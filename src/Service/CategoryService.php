<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Category;
use App\Utils\Validator;
use Exception;
use PDO;
use PDOException;

class CategoryService
{

    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function createCategory(array $data)
    {
        try {
            $Category = new Category($this->pdo);

            $fields = Validator::validate([
                "name" => $data['name'] ?? '',
            ]);

            if (isset($data['description'])) $fields['description'] = $data['description'];

            if (isset($data['parent_category'])) {
                if ($data['parent_category'] === null || $data['parent_category'] === '' || empty($data['parent_category'])) {
                    $fields['parent_category'] = null;
                } elseif (is_numeric($data['parent_category'])) {
                    $fields['parent_category'] = (int) $data['parent_category'];
                } else {
                    throw new Exception("Digite um valor válido para categoria principal");
                }
            } else {
                $fields['parent_category'] = null;
            }

            $category = $Category->create($data);

            if (!$category) {
                throw new Exception("Não foi possível cadastrar categoria");
            }

            return "Categoria Cadastrada com sucesso!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getAllParent()
    {
        try {
            $Category = new Category($this->pdo);
            $category = $Category->getAllParent();

            return $category;
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getAllCategories(): array
    {
        try {

            $Category = new Category($this->pdo);
            $categoryParent = $Category->getAllParent();
            $category = $Category->getAllCategories();

            $category = array_map(function ($cat) use ($categoryParent) {
                $cat['parent_category'] = null;
                foreach ($categoryParent as $parent) {
                    if ($cat['parent_category_id'] == $parent['id_category']) {
                        $cat['parent_category'] = $parent['name'];
                    }
                }
                return $cat;
            }, $category);

            foreach ($categoryParent as $parent) {
                $exists = array_filter($category, function ($cat) use ($parent) {
                    return $cat['id_category'] == $parent['id_category'];
                });

                if (empty($exists)) {
                    $category[] = [
                        'id_category' => $parent['id_category'],
                        'name' => $parent['name'],
                        'parent_category_id' => null,
                        'parent_category' => null
                    ];
                }
            }

            return $category;
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function update(array $data)
    {
        try {
            $Category = new Category($this->pdo);

            $fields = Validator::validate([
                "id_category" => $data['id_category'] ?? '',
                "name" => $data['name'] ?? '',
                "description" => $data['description'] ?? '',
            ]);

            if (isset($data['description'])) $fields['description'] = $data['description'];

            if (isset($data['parent_category'])) {
                if ($data['parent_category'] === null || $data['parent_category'] === '' || empty($data['parent_category'])) {
                    $fields['parent_category'] = null;
                } elseif (is_numeric($data['parent_category'])) {
                    $fields['parent_category'] = (int) $data['parent_category'];
                } else {
                    throw new Exception("Digite um valor válido para categoria principal");
                }
            } else {
                $fields['parent_category'] = null;
            }

            $category = $Category->update($fields);

            return $category;
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function delete(array $data)
    {
        try {
            $Category = new Category($this->pdo);
            $fields = Validator::validate([
                "id_category" => $data['id_category'] ?? ''
            ]);

            $category = $Category->delete($fields);

            if (!$category) {
                throw new Exception("Não foi possível deletar categoria");
            }

            return "Categoria deletada com sucesso!";
        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
