<?php

namespace app\services;

use PDO;

class MailService
{

    public $headers;
    public function __construct()
    {
        $this->headers = [
            'From' => 'Jacklab_Web@gmail.com',
            'Content-type' => 'text/html'
        ];
    }

    public function sendRegisterMail($name, $account, $password, $token)
    {
        $subject = "創建帳號通知";
        $url = $_ENV['REACT_HOST'] . "/emailvalidate/$account/$token";
        $filepath = dirname(dirname(__DIR__)) . "/view/template/emailvalidate.html";
        $temp = fopen($filepath, "r");
        $content = fread($temp, filesize($filepath));
        $content = str_replace([
            '{{domain}}',
            '{{username}}',
            '{{account}}',
            '{{password}}',
            '{{validateurl}}'
        ], [
            $_ENV['HOST'],
            $name,
            $account,
            $password,
            $url,
        ], $content);
        mail($account, $subject, $content, $this->headers);
    }

    public function sendForgetPasswordMail($name, $email, $code)
    {
        $subject = "{$code} 是你的 Jacklab 驗證碼";
        $filepath = dirname(dirname(__DIR__)) . "/view/template/forgetpassword.html";
        $temp = fopen($filepath, "r");
        $content = fread($temp, filesize($filepath));
        $content = str_replace([
            '{{code}}',
            '{{username}}',
        ], [
            $code,
            $name
        ], $content);
        mail($email, $subject, $content, $this->headers);
    }
}
