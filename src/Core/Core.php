<?php

namespace App\Core;

use App\Factory\ConnectionFactory;
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

        $request = new Request();
        $response = new Response();
        $connection = ConnectionFactory::getConnection();

        $url = '/';

        isset($_GET['url']) && $url .= $_GET['url'];

        $url !== '/' && $url = rtrim($url, '/');
        
        $prefixController = 'App\\Controllers\\';
        $routeFound = false;

        foreach ($routes as $route) {
            
            if ($route['method'] !== Request::method()) {
                continue;
            }
            
            $pattern = '#^' . str_replace('{param}', '([\w-]+)', $route['path']) . '$#';
            
            if (preg_match($pattern, $url, $matches)) {
                $routeFound = true;
                array_shift($matches);
                
                // if ($route['method'] !== Request::method()) {
                    //     Response::json([
                        //         'success' => false,
                        //         'message' => 'Desculpe, método não encontrado!'
                        //     ], 405);
                        //     exit;
                        // }
                        
                        if (isset($route['middlewares']) && !empty($route['middlewares'])) {
                            foreach ($route['middlewares'] as $middleware) {
                        $middlewareClass = new $middleware($connection);
                        
                        if (!$middlewareClass->handle($request, $response)) {
                            exit;
                        }
                    }
                }
                
                [$controller, $action] = $route['action'];
                
                try {
                    $extendController = new $controller($request, $response, $connection);
                    
                    if (!method_exists($extendController, $action)) {
                        throw new Exception("O método '$action' não existe no controlador '$controller'");
                    }
                    $extendController->$action($matches);
                } catch (Exception $e) {
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
            $notFoundController = new $controller(new Request, new Response);
            $notFoundController->index();
        }
    }
}
