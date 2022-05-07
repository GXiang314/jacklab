<?php

namespace app\core;

class Request
{
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position);
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
        $body = [];
        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                // $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                $body[$key] = $value;
            }
        }
        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                // $body[$key] = filter_input(INPUT_POST, $key, FILTER_DEFAULT);
                $body[$key] = $value;
            }
        }
        return $body;
    }

    /**
     * 
     * request method except multiple form data
     * 
     */
    public function getJson()
    {
        $body = [];
        $data = json_decode(file_get_contents('php://input')) ?? [];

        if ($this->isPost()) {
            foreach ($data as $key => $value) {
                // $body[$key] = filter_input(INPUT_POST, $value, FILTER_DEFAULT);
                $body[$key] = $value;
            }
        }
        if ($this->isPut()) {
            foreach ($data as $key => $value) {
                // $body[$key] = filter_input(INPUT_POST, $value, FILTER_DEFAULT);
                $body[$key] = $value;
            }
        }
        return $body;
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
        return $this->method() === 'PUT';
    }

    public function isDelete()
    {
        return $this->method() === 'DELETE';
    }
}
