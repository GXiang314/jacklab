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

        http_response_code($status);
        return json_encode($params);
    }
}