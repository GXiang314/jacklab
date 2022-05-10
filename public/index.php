<?php
namespace app\public;

use app\controllers\api\AcademicController;
use app\controllers\api\ClassesController;
use app\controllers\api\DownloadController;
use app\controllers\api\LoginController;
use app\controllers\api\MeetController;
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


$app->router->get('/api/emailvalidate?', [MemberController::class,'emailvalidate']); //信箱驗證
$app->router->post('/api/login', [LoginController::class,'login']); //登入
$app->router->post('/api/forgetPassword', [MemberController::class,'forgetPassword']); //發送重設密碼請求
$app->router->post('/api/resetCodeValidate', [MemberController::class,'resetCodeValidate']); //重設密碼驗證
$app->router->post('/api/resetPassword', [MemberController::class,'resetPassword']); //重設密碼


$app->router->get('/api/member', [MemberController::class,'index']); //取得所有會員公開資料
$app->router->get('/api/member?', [MemberController::class,'show']); //取得該會員公開資料
$app->router->put('/api/member/pwd', [MemberController::class,'updatePassword']); //會員更改密碼
$app->router->put('/api/member/info', [MemberController::class,'updateIntroduction']); //會員更改個人簡介


$app->router->get('/api/manager/user', [UserController::class,'index']); //取得所有會員資料
$app->router->post('/api/manager/useradd', [UserController::class,'useradd']); //加入學生
$app->router->delete('/api/manager/user?', [UserController::class,'destroy']); //刪除使用者


$app->router->get('/api/academic', [AcademicController::class,'index']); //取得所有學制
$app->router->get('/api/academic?', [AcademicController::class,'show']); //取得該學制班級
$app->router->post('/api/academic', [AcademicController::class,'store']); //新增學制
$app->router->put('/api/academic', [AcademicController::class,'update']); //修改學制
$app->router->delete('/api/academic?', [AcademicController::class,'destroy']); //刪除學制

$app->router->get('/api/class', [ClassesController::class,'index']); //取得所有班級
$app->router->get('/api/class?', [ClassesController::class,'show']); //取得該班級學生
$app->router->post('/api/class', [ClassesController::class,'store']); //新增班級
$app->router->put('/api/class', [ClassesController::class,'update']); //修改班級
$app->router->delete('/api/class?', [ClassesController::class,'destroy']); //刪除班級

$app->router->get('/api/meeting', [MeetController::class,'index']); //取得會議列表
$app->router->get('/api/meeting?', [MeetController::class,'show']); //取得該會議記錄
$app->router->post('/api/meeting', [MeetController::class,'store']); //新增會議記錄
$app->router->put('/api/meeting', [MeetController::class,'update']); //修改會議記錄
$app->router->delete('/api/meeting?', [MeetController::class,'destroy']); //刪除會議記錄(軟刪除)
$app->router->get('/api/download/meeting?', [DownloadController::class,'download_Meet']); //下載會議記錄檔案


$app->run();