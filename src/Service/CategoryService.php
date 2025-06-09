<?php

namespace App\Service;

use App\Model\Category;
use App\Service\Base\BaseService;
use App\Utils\Validator;
use Exception;

class CategoryService extends BaseService
{

    public function createCategory(array $data)
    {

        return $this->execute(function () use ($data) {
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
        });
    }

    public function getAllParent()
    {
        return $this->execute(function () {
            $Category = new Category($this->pdo);
            $category = $Category->getAllParent();

            return $category;
        });
    }

    public function getAllCategories(string $rule): array
    {
        return $this->execute(function () use ($rule) {
            $Category = new Category($this->pdo);
            echo json_encode($rule);
            $categoryParent = $Category->getAllCategoriesHasProducts($rule == "COMMON", true);
            $category = $Category->getAllCategoriesHasProducts($rule == "COMMON", false);

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
        });
    }

    public function update(array $data)
    {
        return $this->execute(function () use ($data) {
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
        });
    }

    public function delete(array $data)
    {
        return $this->execute(function () use ($data) {
            $Category = new Category($this->pdo);
            $fields = Validator::validate([
                "id_category" => $data['id_category'] ?? ''
            ]);

            $category = $Category->delete($fields);

            if (!$category) {
                throw new Exception("Não foi possível deletar categoria");
            }

            return "Categoria deletada com sucesso!";
        });
    }

    public function getCategory(int $id)
    {
        return $this->execute(function () use ($id) {
            $Category = new Category($this->pdo);
            $result = $Category->getCategory($id);

            if (!$result) {
                throw new Exception("Não foi possível resgatar categoria");
            }

            return $result;
        });
    }
}
