<?php

namespace Components;

use Helpers\RequestHelper;

class Route
{
    private static $routes;
    private static $version = 1;

    private static function register($path, $method, $controller,$custom_handler) {

        $path = "api/v" . self::$version . "/" . $path;
        self::$routes[$path][$method]["controller"] = $controller;
        self::$routes[$path][$method]["custom_handler"] = $custom_handler;


    }

    public static function setVersion($version_number) {

        if ($version_number == null || !is_integer($version_number)) {

            throw new \Exception("[Bad use] Version number can not be null or none integer !");
        }
        self::$version = $version_number;
    }

    public static function GET($path, $controller,$custom_handler=null) {

        self::register($path, "GET", $controller,$custom_handler);
    }

    public static function POST($path, $controller,$custom_handler=null) {

        self::register($path, "POST", $controller,$custom_handler);
    }

    public static function PUT($path, $controller,$custom_handler=null) {

        self::register($path, "PUT", $controller,$custom_handler);
    }
    public static function DELETE($path, $controller,$custom_handler=null) {

        self::register($path, "DELETE", $controller,$custom_handler);
    }

    private static function mapPathWithParams($request_path_parts) {

        foreach (self::$routes as $path => $methodControllers) {
            $exploded_registered_path = explode("/", $path);

            if (sizeof($exploded_registered_path) != sizeof($request_path_parts)) {
                continue;
            }

            $params = [];
            $isMatch = true;

            foreach($exploded_registered_path as $index => $item) {

                if (str_starts_with($item, "{") && str_ends_with($item, "}"))
                {
                    $param_key = substr($item, 1, strlen($item) - 2);
                    $params[$param_key] = $request_path_parts[$index];
                    continue;
                }
                if ($item != $request_path_parts[$index])
                {

                    $isMatch = false;
                    break;
                }
            }

            if ($isMatch) {

                return [
                    'path' => $path,
                    'params' => $params
                ];
            }
        }

        return [
            'path' => '',
            'params' => ''
        ];
    }


    public static function handleRequest() {


        $uri = RequestHelper::getUriWithoutQueryParams();
        $path_parts = RequestHelper::getRequestUriAsArray($uri,true);
        $mapped_path_params = self::mapPathWithParams($path_parts);
        $request_path = $mapped_path_params['path'];
        $request_params = $mapped_path_params['params'];
        $request_method = $_SERVER['REQUEST_METHOD'];

        if (! $request_path) {

            return ["message" => "request is not found."];
        }
        elseif (! key_exists($request_method, self::$routes[$request_path])) {

            return ["message" => "request isn't registered with " . $request_method . " method"];
        }
        else {

            $controller = self::$routes[$request_path][$request_method]["controller"];
            $custom_handler=self::$routes[$request_path][$request_method]["custom_handler"];

            if($custom_handler != null){

                $request_method=$custom_handler;

            }
            return (new $controller())->$request_method($request_params);
        }
    }
}

