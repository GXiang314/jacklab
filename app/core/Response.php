<?php

namespace app\core;

class Response
{

    public static function setStatusCode(int $code)
    {
        return http_response_code($code);
    }

    public static function json(array $params, $status = 200)
    {
        header('Content-type: application/json');
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 dayself::setStatusCode($status);

        return json_encode($params);
    }
}