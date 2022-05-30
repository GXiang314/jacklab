<?php

namespace app\core\Exception;

use app\core\Response;

class InternalServerErrorException extends \Exception{

    protected $code = 500;
    public function __construct($message = "Internal Server Error.")
    {
        echo Response::json(['message' => $message], $this->code);
        exit;
    }
}