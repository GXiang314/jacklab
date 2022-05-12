<?php
namespace app\services;

use PDO;

class MailService{

    public $headers;
    public function __construct()
    {
        $this->headers = [
            'From'=> 'Jacklab_Web@gmail.com',
            'Name'=> 'Jacklab_Web',
        ];
    }

    public function sendRegisterMail($name, $account,$password, $token)
    {
        $subject = "創建帳號通知";
        $url = $_ENV['LOCAL_HOST']."/api/emailvalidate?email=$account&token=$token";
        $content = "使用者「{$name}」，你好：\r\n\r\n您的帳號是：{$account}\r\n您的密碼是：{$password}\r\n\r\n請點擊下方連結以完成驗證步驟\r\n{$url}";
        mail($account, $subject, $content, $this->headers);
    }

    public function sendForgetPasswordMail($name, $email, $code)
    {
        $subject = "{$code} 是你的 Jacklab 驗證碼";
        $content = "使用者「{$name}」，你好：\r\n\r\n我們已收到你的重設密碼要求。\r\n請輸入下方驗證碼重設密碼(30分鐘內有效)：\r\n\r\n{$code}";
        mail($email, $subject, $content, $this->headers);
        
    }
}