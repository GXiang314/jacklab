<?php
namespace app\services;

class MailService{

    public $headers;
    public function __construct()
    {
        $this->header = "From: Jacklab_Web@gmail.com";
    }

    public function sendRegisterMail($name, $account,$password, $token)
    {
        $subject = "創建帳號通知";
        $url = $_ENV['HOST']."/emailvalidate/?email=$account&token=$token";
        $content = "使用者「{$name}」，您好：\r\n\r\n您的帳號是：{$account}\r\n您的密碼是：{$password}\r\n\r\n請點擊以下連結以完成驗證步驟：\r\n{$url}";
        mail($account, $subject, $content, $this->header);
    }


}