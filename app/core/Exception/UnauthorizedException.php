<?php

namespace app\core\Exception;

use app\core\Response;

class UnauthorizedException extends \Exception
{

    protected $code = 401;
    public function __construct($message = "401 Unauthorized.")
    {
        echo Response::json(['message' => $message], $this->code);
        exit;
    }
}
