<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Service\MediaService;

class MediaController{

    public function getContentFolder(Request $request, Response $response)
    {
        $body = $request::body();

        $MediaService = MediaService::getAllInFolder($body);

        if(isset($MediaService['error'])){
            return $response::json([
                "success" => false,
                "message" => $MediaService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $MediaService
        ]);
    }
    // Folders
    public function createFolder(Request $request, Response $response){
        $body = $request::body();

        $MediaService = MediaService::createFolder($body);

        if(isset($MediaService['error'])){
            return $response::json([
                "success" => false,
                "message" => $MediaService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $MediaService
        ]);
    }
    public function renameFolder(Request $request, Response $response)
    {
        $body = $request::body();

        $MediaService = MediaService::editFolder($body);

        if(isset($MediaService['error'])){
            return $response::json([
                "success" => false,
                "message" => $MediaService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $MediaService
        ]);
    }
    public function moveFolder(Request $request, Response $response)
    {
        $body = $request::body();

        $MediaService = MediaService::moveFolder($body);

        if(isset($MediaService['error'])){
            return $response::json([
                "success" => false,
                "message" => $MediaService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $MediaService
        ]);
    }
    public function moveFolderTrash(Request $request, Response $response)
    {
        $body = $request::body();

        $MediaService = MediaService::moveFolderToTrash($body);

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

    public function restoreFolder(Request $request, Response $response)
    {
        $body = $request::body();

        $MediaService = MediaService::restoreFolder($body);

        if(isset($MediaService['error'])){
            return $response::json([
                "success" => false,
                "message" => $MediaService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $MediaService
        ]);
    }

    public function deleteFolder(Request $request, Response $response)
    {
        $body = $request::body();

        $MediaService = MediaService::deleteFolder($body);

        if(isset($MediaService['error'])){
            return $response::json([
                "success" => false,
                "message" => $MediaService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $MediaService
        ]);
    }
    // Files
    public function uploadFile(Request $request, Response $response)
    {
        $body = $request::body();

        $MediaService = MediaService::uploadFile($body);

        if(isset($MediaService['error'])){
            return $response::json([
                "success" => false,
                "message" => $MediaService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $MediaService
        ]);
    }
    public function renameFile(Request $request, Response $response)
    {
        $body = $request::body();

        $MediaService = MediaService::editFile($body);

        if(isset($MediaService['error'])){
            return $response::json([
                "success" => false,
                "message" => $MediaService['error']
            ], 400);
        }

        $response::json(data: [
            "success" => true,
            "message" => $MediaService
        ]);
    }
    public function moveFile(Request $request, Response $response)
    {
        $body = $request::body();

        $MediaService = MediaService::moveFile($body);

        if(isset($MediaService['error'])){
            return $response::json( [
                "success" => false,
                "message" => $MediaService['error']
            ], 400);
        }

        $response::json( [
            "success" => true,
            "message" => $MediaService
        ]);
    }
    public function moveFileTrash(Request $request, Response $response)
    {
        $body = $request::body();

        $MediaService = MediaService::moveFileToTrash($body);

        if(isset($MediaService['error'])){
            return $response::json([
                "success" => false,
                "message" => $MediaService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $MediaService
        ]);
    }
    public function restoreFile(Request $request, Response $response)
    {
        $body = $request::body();

        $MediaService = MediaService::restoreFile($body);

        if(isset($MediaService['error'])){
            return $response::json([
                "success" => false,
                "message" => $MediaService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $MediaService
        ]);
    }
    public function deleteFile(Request $request, Response $response)
    {
        $body = $request::body();

        $MediaService = MediaService::deleteFile($body);

        if(isset($MediaService['error'])){
            return $response::json([
                "success" => false,
                "message" => $MediaService['error']
            ], 400);
        }

        $response::json([
            "success" => true,
            "message" => $MediaService
        ]);
    }
}