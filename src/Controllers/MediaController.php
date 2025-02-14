<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Service\MediaService;

class MediaController{
    
    public function createFolder(Request $request, Response $response){
        $body = Request::body();

        $MediaService = MediaService::createFolder($body);

        if(isset($MediaService['error'])){
            return $response::json([
                "success" => false,
                "message" => $MediaService['error']
            ], 400);
        }

        $response::json([
            "success" => false,
            "message" => $MediaService
        ]);
    }

}