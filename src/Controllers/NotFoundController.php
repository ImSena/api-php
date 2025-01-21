<?php

namespace App\Controllers;
use App\Http\Request;
use App\Http\Response;

class NotFoundController
{
    public function index(Request $request, Response $response)
    {
       $response::json([
        'success' => false,
        'message' => "Desculpe, rota não encontrada."
       ], 404);
       return;
    }
}