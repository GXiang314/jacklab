<?php

namespace app\core;

class Router
{
    protected array $routes = [];
    public Response $response;
    public Request $request;
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function put($path, $callback)
    {
        $this->routes['PUT'][$path] = $callback;
    }

    public function delete($path, $callback)
    {
        $this->routes['DELETE'][$path] = $callback;
    }

    public function resolve()
    {
        $method = $this->request->method();
        $path = $this->request->getPath();
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false) {
            return Controller::sendError("404 Not Found.");
        }
        if (is_array($callback)) {
            /**
             * @var Controller $controller
             */
            $controller = new $callback[0]();
            $controller->action = $callback[1];
            Application::$app->controller = $controller;
            $callback[0] = Application::$app->controller;

            foreach($controller->getMiddleware() as $middleware){
                $this->request = $middleware->execute($this->request);
            }
        }
        header("Access-Control-Allow-Origin: ".$_ENV['ALLOW_ORIGIN']);
        header('Access-Control-Max-Age: 86400');    // cache for 1 dayself::setStatusCode($status);
        header("Access-Control-Allow-Headers:Origin,Content-Type, access-control-allow-origin, Authorization, X-Requested-With");
        if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            exit();
        }
        return (call_user_func($callback, $this->request));
    }
}
