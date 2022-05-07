<?php

namespace app\core;

// use Exception;
// use ReflectionClass;

class Application
{
    public static Application $app;
    public static string $ROOT_DIR;
    public Router $router;
    public Request $request;
    public Response $response;
    public Database $db;
    
    public function __construct(array $config)
    {
        self::$app = $this;
        self::$ROOT_DIR = dirname(__DIR__);
        $this->response = new Response();
        $this->request = new Request();
        $this->router = new Router($this->request, $this->response);
        $this->db = new Database($config['db']);
    }

    public function run()
    {
        echo $this->router->resolve();
    }
}
