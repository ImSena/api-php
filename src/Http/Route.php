<?php

namespace App\Http;

class Route
{
    private static array $routes = [];

    public static function get(string $path, array $action, array $middlewares = [])
    {
        self::$routes[] = [
            'path' => $path,
            'action' => $action,
            'method' => 'GET',
            'middlewares' => $middlewares
        ];
    }

    public static function post(string $path, array $action, array $middlewares = [])
    {
        self::$routes[] = [
            'path' => $path,
            'action' => $action,
            'method' => 'POST',
            'middlewares' => $middlewares
        ];
    }

    public static function put(string $path, array $action, array $middlewares = [])
    {
        self::$routes[] = [
            'path' => $path,
            'action' => $action,
            'method' => 'PUT',
            'middlewares' => $middlewares
        ];
    }

    public static function delete(string $path, array $action, array $middlewares = [])
    {
        self::$routes[] = [
            'path' => $path,
            'action' => $action,
            'method' => 'DELETE',
            'middlewares' => $middlewares
        ];
    }

    public static function group(array $options, callable $callback)
    {
        $middlewares = $options['middlewares'] ?? [];
        $prefix = $options['prefix'] ?? '';

        $callback($prefix, $middlewares);
    }

    public static function routes()
    {
        return self::$routes;
    }

}