<?php

namespace app\core\Exception;

use app\core\Response;

class NotFoundExceotion extends \Exception{

    protected $code = 404;
    public function __construct($message = "404 Not Found.")
    {
        echo Response::json(['message' => $message], $this->code);
        exit;
    }
}