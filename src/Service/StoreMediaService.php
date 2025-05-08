<?php

namespace App\Service;

use App\Model\Media;
use App\Model\Store;
use App\Model\StoreMedia;
use App\Service\Base\BaseService;
use App\Utils\Validator;
use Exception;

class StoreMediaService extends BaseService
{
    public function createMedia(array $data, bool $isTransaction = false)
    {
        return $this->execute(function () use ($data) {
            $types = [
                'LOGO',
                'LOGO_FOOTER',
                'FAVICON',
            ];

            $fields = Validator::validate([
                "id_media" => $data['id_media'],
                "type" => $data['type'],
            ]);

            if (!in_array($fields['type'], $types)) {
                throw new Exception("Tipo de midia inválido.");
            }

            $StoreMedia = new StoreMedia($this->pdo);

            $StoreMedia->inactiveMedia($fields);

            if (!$StoreMedia->create($fields)) {
                throw new Exception("Não foi possível inserir a mídia.");
            }

            return "Media criada com sucesso";
        }, $isTransaction);
    }

    public function getIdentity()
    {
        return $this->execute(function () {
            $medias = $this->getMedias();

            return [
                "LOGO" => isset($medias['LOGO']) ? $medias['LOGO'] : null,
                "LOGO_FOOTER" => isset($medias['LOGO_FOOTER']) ? $medias['LOGO_FOOTER'] : null,
                "FAVICON" => isset($medias['FAVICON']) ? $medias['FAVICON'] : null,
            ];
        });
    }

    private function getMedias()
    {
        return $this->execute(function () {
            $StoreMedia = new StoreMedia($this->pdo);
            $Media = new Media($this->pdo);
            $MediaService = new MediaService($this->pdo);

            $types = [
                'LOGO',
                'LOGO_FOOTER',
                'FAVICON',
            ];

            $medias = [];

            foreach ($types as $type) {
                $mediaStore = $StoreMedia->getMedia($type);
                if (!$mediaStore) {
                    continue;
                }

                $file = $Media->getFile($mediaStore['id_media']);

                if (!$file) {
                    throw new Exception("Não foi possível resgatar midia");
                }

                $path = $Media->getPathToFile($mediaStore);
                $extension = $MediaService->getExtension($file['file_type']);
                $picture = $path . '.' . $extension;

                switch ($mediaStore['type']) {
                    case "LOGO":
                        $medias["LOGO"] = [
                            "type" => $mediaStore['type'],
                            "path" => $picture
                        ];
                        break;
                    case "LOGO_FOOTER":
                        $medias['LOGO_FOOTER'] = [
                            "type" => $mediaStore['type'],
                            "path" => $picture
                        ];
                        break;
                    case "FAVICON":
                        $medias['FAVICON'] = [
                            "type" => $mediaStore['type'],
                            "path" => $picture
                        ];
                        break;
                }
            }

            return $medias;
        });
    }

}
