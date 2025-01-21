<?php

namespace App\Core;

use App\Http\Request;
use App\Http\Response;
use Exception;

class Core
{
    public static function dispatch(array $routes)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        $url = '/';

        isset($_GET['url']) && $url .= $_GET['url'];

        $url !== '/' && $url = rtrim($url, '/');

        $prefixController = 'App\\Controllers\\';
        $routeFound = false;

        foreach ($routes as $route) {
            $pattern = '#^' . preg_replace("/{id}/", '([\w-]+)', $route['path']) . '$#';
            
            if (preg_match($pattern, $url, $matches)) {
                $routeFound = true;
                array_shift($matches);

                if ($route['method'] !== Request::method()) {
                    Response::json([
                        'success' => false,
                        'message' => 'Desculpe, método não encontrado!'
                    ], 405);
                    exit;
                }

                if(isset($route['middlewares']) && !empty($route['middlewares'])){
                    foreach($route['middlewares'] as $middleware);
                    $middlewareClass = new $middleware();

                    if(!$middlewareClass->handle(new Request, new Response)){
                        exit;
                    }
                }

                [$controller, $action] = $route['action'];

                try{
                    $extendController = new $controller();
                    
                    if (!method_exists($extendController, $action)) {
                        throw new \Exception("O método '$action' não existe no controlador '$controller'");
                    }
                    $extendController->$action(new Request, new Response, $matches);
                }catch(Exception $e){
                    $message = $e->getMessage();
                    Response::json([
                        'success' => false,
                        'message' => $message
                    ], 500);
                }

                return;
            }
        }
        
        if (!$routeFound) {
            $controller = $prefixController . "NotFoundController";
            $notFoundController = new $controller();
            $notFoundController->index(new Request, new Response);
        }
    }
}
