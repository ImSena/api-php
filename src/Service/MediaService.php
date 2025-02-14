<?php

namespace App\Service;

use App\Utils\Validator;

class MediaService{
    public static function createFolder(array $data){

        $fields = Validator::validate([
            "folder_name" => $data['folder_name'] ?? '',
        ]);

        if(isset($data['parent_id'])){
            $fields['parent_id'] = $data['parent_id'];
        }

        

    }
}