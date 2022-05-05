<?php 

namespace app\core;

class Request{
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path,'?');
        if($position === false){
            return $path;
        }
        return substr($path,0,$position);

    }

    public function getMethod()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) ;
    }

    public function getBody()
    {
        $body = [];
        if($this->getMethod() === 'GET'){
            foreach($_GET as $key =>$value){
                $body[$key] = filter_input(INPUT_GET,$key,FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if($this->getMethod() === 'POST'){
            foreach($_POST as $key => $value){
                $body[$key] = filter_input(INPUT_GET,$key,FILTER_DEFAULT);
            }
        }
        var_dump($body);
        return $body;
    }
}