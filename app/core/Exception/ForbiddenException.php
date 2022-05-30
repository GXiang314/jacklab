<?php

namespace app\core\Exception;

use app\core\Response;

class ForbiddenException extends \Exception{

    protected $code = 403;
    public function __construct($message = "You dont have permission to access this page.")
    {
        echo Response::json(['message' => $message], $this->code);
        exit;
    }
}