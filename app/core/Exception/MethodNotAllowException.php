<?php

namespace app\core\Exception;

use app\core\Response;

class MethodNotAllowException extends \Exception{

    protected $code = 405;
    public function __construct($message = "405 Method Not Allow.")
    {
        echo Response::json(['message' => $message], $this->code);
        exit;
    }
}