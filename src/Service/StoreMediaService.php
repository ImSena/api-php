<?php

namespace App\Service;

use App\Helpers\DatabaseErrorHelpers;
use App\Model\Media;
use App\Model\StoreMedia;
use App\Utils\Validator;
use Exception;
use PDO;
use PDOException;

class StoreMediaService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createMedia(array $data)
    {
        try {

            $this->pdo->beginTransaction();

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

            $this->pdo->commit();

            return "Media criada com sucesso";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function getMedias()
    {
        try {
            
            $medias = $this->getIdentity();
            $config = $this->getConfig();

            return [
                "LOGO" => isset($medias['LOGO']) ? $medias['LOGO'] : null,
                "LOGO_FOOTER" => isset($medias['LOGO_FOOTER']) ? $medias['LOGO_FOOTER'] : null,
                "FAVICON" => isset($medias['FAVICON']) ? $medias['FAVICON'] : null,
                "THEME" => isset($config['THEME']) ? $config['THEME'] : null,
                'LAYOUT' => isset($config['LAYOUT']) ? $config['LAYOUT'] : null
            ];

        } catch (PDOException $e) {
            return ['error' => DatabaseErrorHelpers::error($e)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function getConfig()
    {
        try{

            return [
                "THEME" => "blue",
                "LAYOUT" => "layout_teste"
            ];
        }catch(PDOException $e){
            throw new PDOException($e);
        }catch(Exception $e){
            throw new Exception($e);
        }
    }

    private function getIdentity(){
        try{
            $StoreMedia = new StoreMedia($this->pdo);
            $Media = new Media($this->pdo);
            $MediaService = new MediaService($this->pdo);
    
            $types = [
                'LOGO',
                'LOGO_FOOTER',
                'FAVICON',
            ];
    
            $medias = [];
    
            foreach($types as $type){
                $mediaStore = $StoreMedia->getMedia($type);
                if(!$mediaStore){
                    continue;
                }
                
                $file = $Media->getFile($mediaStore['id_media']);
                
                if(!$file){
                    throw new Exception("Não foi possível resgatar midia");
                }
    
                $path = $Media->getPathToFile($mediaStore);
                $extension = $MediaService->getExtension($file['file_type']);
                $picture = $path . '.' . $extension;
    
                switch($mediaStore['type']){
                    case "LOGO":
                        $medias["LOGO"] = [
                            "type" => $mediaStore['type'],
                            "path" => $picture
                        ];
                    break;
                    case "LOGO_FOOTER" :
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
        }catch(PDOException $e){
            throw $e;
        }catch(Exception $e){
            throw $e;
        }
    }
}
