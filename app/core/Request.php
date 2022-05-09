<?php

namespace app\core;

class Request
{
    public array $body =[];

    public function addKeys($array = []){
        foreach($array as $key => $value){
            $this->body[$key] = $value;
        }
    }

    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position + 1);
    }

    public function method()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }
    /**
     * 
     * request method only multiple form data & x-www-form-urlencode
     * 
     */
    public function getBody()
    {
        if ($this->isGet()||$this->isDelete()) {
            foreach ($_GET as $key => $value) {
                // $this->body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                $this->body[$key] = $value;
            }
        }else if ($this->isPost() || $this->isPut()) {
            foreach ($_POST as $key => $value) {
                // $this->body[$key] = filter_input(INPUT_POST, $key, FILTER_DEFAULT);
                $this->body[$key] = $value;
            }
        }
        return $this->body;
    }

    /**
     * 
     * request method except multiple form data
     * 
     */
    public function getJson()
    {
        $data = json_decode(file_get_contents('php://input')) ?? [];       
        foreach ($data as $key => $value) {
            // $this->body[$key] = filter_input(INPUT_POST, $value, FILTER_DEFAULT);
            $this->body[$key] = $value;
        }
        return $this->body;
    }



    public function isGet()
    {
        return $this->method() === 'GET';
    }

    public function isPost()
    {
        return $this->method() === 'POST';
    }

    public function isPut()
    {
        return $this->method() === 'PUT' || $this->getBody()['_method'] ?? '' === 'PUT';
    }

    public function isDelete()
    {
        return $this->method() === 'DELETE';
    }

    /**
     * get HTTP Header from request.
     */
    public function header($httpHeader){
        $httpHeader = "HTTP_".strtoupper($httpHeader);
        return $_SERVER[$httpHeader] ?? null;
    }
}
