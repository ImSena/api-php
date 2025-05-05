<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;
use App\Service\MediaService;

class MediaController extends BaseController{

    public function getContentFolder()
    {
        $body = $this->request::body();

        $MediaService = new MediaService($this->pdo);
        $MediaService = $MediaService->getAllInFolder($body);

        if(isset($MediaService['error'])){
            return $this->errorResponse($MediaService['error']);
        }

        return $this->successResponse("Operação realizada com sucesso.", $MediaService);

    }
    
    // Folders
    public function createFolder(){
        $body = $this->request::body();

        $MediaService = new MediaService($this->pdo);
        $MediaService = $MediaService->createFolder($body);

        if(isset($MediaService['error'])){
            return $this->errorResponse($MediaService['error']);
        }

        $this->successResponse($MediaService);
    }
    public function renameFolder()
    {
        $body = $this->request::body();

        $MediaService = new MediaService($this->pdo);
        $MediaService = $MediaService->editFolder($body);

        if(isset($MediaService['error'])){
            return $this->errorResponse($MediaService['error']);
        }

        return $this->successResponse($MediaService);
    }
    public function moveFolder()
    {
        $body = $this->request::body();

        $MediaService = new MediaService($this->pdo);
        $MediaService = $MediaService->moveFolder($body);

        if(isset($MediaService['error'])){
            return $this->errorResponse($MediaService['error']);
        }

        return $this->successResponse($MediaService);
    }
    public function moveFolderTrash()
    {
        $body = $this->request::body();

        $MediaService = new MediaService($this->pdo);
        $MediaService = $MediaService->moveFolderToTrash($body);

        if(isset($MediaService['error'])){
            return $this->errorResponse($MediaService['error']);
        }

        return $this->successResponse($MediaService);
    }

    public function restoreFolder()
    {
        $body = $this->request::body();

        $MediaService = new MediaService($this->pdo);
        $MediaService = $MediaService->restoreFolder($body);

        if(isset($MediaService['error'])){
            return $this->errorResponse($MediaService['error']);
        }

        return $this->successResponse($MediaService);
    }

    public function deleteFolder()
    {
        $body = $this->request::body();

        $MediaService = new MediaService($this->pdo);
        $MediaService = $MediaService->deleteFolder($body);

        if(isset($MediaService['error'])){
            return $this->errorResponse($MediaService['error']);
        }

        return $this->successResponse($MediaService);
    }
    // Files
    public function uploadFile()
    {
        $body = $this->request::body();
        $files = $this->request::files();

        $MediaService = new MediaService($this->pdo);
        $MediaService = $MediaService->uploadFile($body, $files);

        if(isset($MediaService['error'])){
            return $this->errorResponse($MediaService['error']);
        }

        return $this->successResponse($MediaService);
    }
    public function renameFile()
    {
        $body = $this->request::body();

        $MediaService = new MediaService($this->pdo);
        $MediaService = $MediaService->editFile($body);

        if(isset($MediaService['error'])){
            return $this->errorResponse($MediaService['error']);
        }

        return $this->successResponse($MediaService);
    }
    public function moveFile()
    {
        $body = $this->request::body();

        $MediaService = new MediaService($this->pdo);
        $MediaService = $MediaService->moveFile($body);

        if(isset($MediaService['error'])){
            return $this->errorResponse($MediaService['error']);
        }

        return $this->successResponse($MediaService);
    }
    public function moveFileTrash()
    {
        $body = $this->request::body();

        $MediaService = new MediaService($this->pdo);
        $MediaService = $MediaService->moveFileToTrash($body);

        if(isset($MediaService['error'])){
            return $this->errorResponse($MediaService['error']);
        }

        return $this->successResponse($MediaService);
    }
    public function restoreFile()
    {
        $body = $this->request::body();

        $MediaService = new MediaService($this->pdo);
        $MediaService = $MediaService->restoreFile($body);

        if(isset($MediaService['error'])){
            return $this->errorResponse($MediaService['error']);
        }

        return $this->successResponse($MediaService);
    }
    public function deleteFile()
    {
        $body = $this->request::body();

        $MediaService = new MediaService($this->pdo);
        $MediaService = $MediaService->deleteFile($body);

        if(isset($MediaService['error'])){
            return $this->errorResponse($MediaService['error']);
        }

        return $this->successResponse($MediaService);
    }
}