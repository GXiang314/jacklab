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
            '{{username}}',
            '{{account}}',
            '{{password}}',
            '{{validateurl}}'
        ], [
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
        $content = "使用者「{$name}」，你好：\r\n\r\n我們已收到你的重設密碼要求。\r\n請輸入下方驗證碼重設密碼(30分鐘內有效)：\r\n\r\n{$code}";
        mail($email, $subject, $content, $this->headers);
    }
}
