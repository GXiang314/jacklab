<?php 

namespace app\core;

use app\core\Request;

abstract class Middleware{

    abstract public function execute(Request $request);

}