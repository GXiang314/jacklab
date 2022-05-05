<?php

namespace app\core;

class Response{

    public static function setStatusCode(int $code)
    {
        return http_response_code($code);
    }


}
