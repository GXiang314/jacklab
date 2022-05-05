<?php

namespace app\core;
use app\controllers\api;
use app\controllers;

class Router{
    protected array $routes = [];
    public Response $response;
    public Request $request;
    public function __construct(Request $request,Response $response)
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


    public function resolve(){
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if($callback === false){
            $this->response->setStatusCode(404);
            return "Not Found.";
            exit;
        }
        return (call_user_func($callback)) ;
    }
}
// class route
// {
//     public function __construct()
//     {
//         $this->routes[$this->httpMethod[0]] = [];
//         $this->routes[$this->httpMethod[1]] = [];
//         $this->routes[$this->httpMethod[2]] = [];
//         $this->routes[$this->httpMethod[3]] = [];
//     }
    

//     public $routes = [];
//     private $httpMethod = [
//         'GET',
//         'POST',
//         'PUT',
//         'DELETE'
//     ];

//     const NOT_FOUND = 0;
//     const FOUND = 1;
//     const METHOD_NOT_ALLOWED = 2;

//     public function get($path, $callback)
//     {
//         array_push($this->routes[$this->httpMethod[0]], [$path => $callback]);
//     }

//     public function post($path, $callback)
//     {
//         array_push($this->routes[$this->httpMethod[1]], [$path => $callback]);
//     }

//     public function put($path, $callback)
//     {
//         array_push($this->routes[$this->httpMethod[2]], [$path => $callback]);
//     }

//     public function delete($path, $callback)
//     {
//         array_push($this->routes[$this->httpMethod[3]], [$path => $callback]);
//     }

//     public function NotFound()
//     {
//         echo "404 Not Found";
//     }

//     public function MethodNotAllow()
//     {
//         echo "405 Method Not Allow";
//     }

//     public function isStaticRoute($arr)
//     {       
//         $res = true;
//         foreach ($arr as $part){
//             if(preg_match("/{[\S]+}/",$part)){
//                 $res = false;
//                 continue;
//             }
//         }
//         return $res;
//     }

//     private function routeCompare($requestUri, $compareUri)
//     {
//         $count = count($requestUri);
//         $res = true;
//         for($i=0 ;$i<=$count-2;$i++){
//             if($requestUri[$i] == $compareUri[$i]){
//                 continue;
//             }
//             $res = false;            
//         }                
//         return $res;
//     }

//     public function staticRouteCompare($requestUri, $compareUri)
//     {
//         $count = count($requestUri);
//         $res = true;
//         for($i=0 ;$i<=$count-1;$i++){
//             if($requestUri[$i] == $compareUri[$i]){
//                 continue;
//             }
//             $res = false;            
//         }
//         return $res;
//     }

//     public function run()
//     {
//         $method = $_SERVER['REQUEST_METHOD'];
//         $uri = ltrim($_SERVER['REQUEST_URI'],"/") ;        
//         $url = explode('/', ltrim(urldecode($uri),' '));
//         $requestPathCount = count($url);

//         if (in_array($method, $this->httpMethod)) {
//             foreach ($this->routes[$method] as $row) {
//                 foreach ($row as $path=>$callback){
//                     $path = ltrim($path,"/") ; 
//                     $arr = explode('/', ltrim($path,' '));
//                     $count = count($arr);
//                     if ($count !== $requestPathCount) continue;                
//                     if($this->isStaticRoute($arr)){
//                         if($this->staticRouteCompare($url,$arr)){
//                             return $callback();                        
//                         }
//                         continue;
//                     }else{
//                     if ($this->routeCompare($url,$arr)){
//                         return $callback($url[$requestPathCount-1]);                        
//                     }                       
//                     continue;
//                     }
//                 }                             
//             }
//             return $this->NotFound();
//         }
//         return $this->MethodNotAllow();
//     }

// }
// $route = new route();



// $route->get('/api/login', "");
// $route->get('/api/member', "");
// $route->get('/api/member/{id}', "");
// $route->run();
