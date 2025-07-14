<?php

namespace App\Controllers;

class NotFoundController extends BannerController
{
    public function index()
    {
       return $this->errorResponse("Não foi possível encontrar rota", 404);
    }
}