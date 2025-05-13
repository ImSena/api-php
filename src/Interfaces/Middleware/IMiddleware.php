<?php

namespace App\Interfaces\Middleware;

use App\Http\Request;
use App\Http\Response;
use PDO;

interface IMiddleware{
    public function handle(Request $request, Response $response):bool;
}