<?php

namespace Helpers;
use Mixin\BasicRulesValidation,Mixin\DatabaseRulesValidation;

class RequestHelper
{
    public static function getRequestUri(){
        return $_SERVER["REQUEST_URI"];
    }
    public static function getUriWithoutQueryParams(){

        $exploded_path_and_query_params = explode("?",self::getRequestUri());
        return array_shift($exploded_path_and_query_params);

    }

    public static function getRequestUriAsArray($uri ,$excludeDomain = false) {

        $exploded_request_path = explode("/",$uri);

        array_shift($exploded_request_path);
        if ($excludeDomain) {

            array_shift($exploded_request_path);
        }

        return $exploded_request_path;
    }

    public static function getRequestPayload() {

        $data_as_string_in_json_format = file_get_contents("php://input");

        if (! $data_as_string_in_json_format) {

            return [];
        }
        return json_decode($data_as_string_in_json_format, true);
    }

    public static function extractResourceIdFromRequestPath() {

        $path_parts = self::getRequestUriAsArray(self::getRequestUri());
        $resource = array_pop($path_parts);

        if (ctype_digit($resource)) {

            return $resource;
        }

        return null;
    }

}