<?php
namespace app\public;

use app\controllers\api\LoginController;
use app\core\Application;
use app\controllers\api\MemberController;
use app\controllers\api\UserController;
use Dotenv\Dotenv;

// use app\core\Application;

require_once (__DIR__."../../vendor/autoload.php");


$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();


$config=[
    'db'=>[
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
    ]
];

$app = new Application($config);

$app->router->get('/api/emailvalidate', [MemberController::class,'emailvalidate']); //信箱驗證
$app->router->post('/api/login', [LoginController::class,'login']); //登入
$app->router->post('/api/forgetPassword', [MemberController::class,'forgetPassword']); //發送重設密碼請求
$app->router->post('/api/resetCodeValidate', [MemberController::class,'resetCodeValidate']); //重設密碼驗證
$app->router->post('/api/resetPassword', [MemberController::class,'resetPassword']); //重設密碼


$app->router->get('/api/member', [MemberController::class,'show']);
$app->router->put('/api/member/pwd', [MemberController::class,'updatePassword']);
$app->router->put('/api/member/info', [MemberController::class,'updateIntroduction']);


$app->router->get('/api/manager/user', [UserController::class,'index']);
$app->router->post('/api/manager/useradd', [UserController::class,'useradd']);
$app->router->delete('/api/manager/user', [UserController::class,'destroy']);

$app->run();