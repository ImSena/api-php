<?php

namespace App\Service;

use App\Model\Banner;
use App\Model\Media;
use App\Service\Base\BaseService;
use App\Utils\Validator;
use Exception;

class BannerService extends BaseService
{
    public function create(array $data)
    {
        return $this->execute(function () use ($data) {
            $fields = Validator::validate([
                "id_media" => $data['id_media'] ?? '',
                "is_mobile" => $data['is_mobile'] ?? '',
                "is_default" => $data['is_default'] ?? ''
            ]);

            if (isset($data['name'])) {
                $fields['name'] = $data['name'];
            }

            $Banner = new Banner($this->pdo);

            $result = $Banner->create($fields);

            if (!$result) {
                throw new Exception("Não foi possível criar Banners.");
            }

            return "Banner criados com sucesso";
        });
    }

    public function getBanners()
    {
        return $this->execute(function () {
            $Banner = new Banner($this->pdo);
            $Media = new Media($this->pdo);
            $MediaService = new MediaService($this->pdo);

            $result = $Banner->getBanners();

            foreach ($result as &$banner) {
                $file = $Media->getFile($banner['id_media']);

                $path = $Media->getPathToFile($banner);
                $extension = $MediaService->getExtension($file['file_type']);
                $picture = $path . '.' . $extension;

                unset($banner['id_media']);
                $banner['image_path'] = $picture;
                $banner['is_mobile'] = $banner['is_mobile'] == 0;
                $banner['is_default'] = $banner['is_default'] == 0;
            }

            return $result;
        });
    }

    public function editBanner(array $data)
    {
        return $this->execute(function () use ($data) {

            $fields = Validator::validate([
                "id_media" => $data['id_media'] ?? '',
                "is_mobile" => $data['is_mobile'] ?? '',
                "is_default" => $data['is_default'] ?? '',
                "id_banner" => $data['id_media'] ?? ''
            ]);

            if (isset($data['name'])) {
                $fields['name'] = $data['name'];
            }

            $Banner = new Banner($this->pdo);

            $result = $Banner->update($fields);

            if (!$result) {
                throw new Exception("Não foi possível editar banner.");
            }

            return "Banner editado com sucesso.";
        });
    }

    public function deleteBanner(int $idBanner)
    {
        return $this->execute(function () use ($idBanner){
            $Banner = new Banner($this->pdo);

            $result = $Banner->delete($idBanner);

            if(!$result){
                throw new Exception("Não foi possível deletar banner.");
            }

            return "Banner deletado com sucesso.";
        });
    }
}
