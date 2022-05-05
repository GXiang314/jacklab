<?php

namespace app\core;

use Exception;
use ReflectionClass;

class Application{
    public static Application $app;
    public Router $router;
    public Request $request;
    public Response $response;
    public function __construct(){
        self::$app = $this;
        $this->response = new Response();
        $this->request = new Request();
        $this->router = new Router($this->request,$this->response);

    }
    public function run()
    {
        echo $this->router->resolve();
    }

   
}