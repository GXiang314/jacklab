<?php 

namespace app\core\middlewares;

use app\core\Request;

abstract class Middleware{

    abstract public function execute(Request $request);

}