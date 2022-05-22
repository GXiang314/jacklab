<?php

namespace app\public;

use Dotenv\Dotenv;
use app\core\Application;
use app\controllers\api\AcademicController;
use app\controllers\api\AlbumController;
use app\controllers\api\BookController;
use app\controllers\api\ClassesController;
use app\controllers\api\DownloadController;
use app\controllers\api\GameManagerController;
use app\controllers\api\GameRecordController;
use app\controllers\api\LabInfoController;
use app\controllers\api\LoginController;
use app\controllers\api\MeetController;
use app\controllers\api\MemberController;
use app\controllers\api\PermissionController;
use app\controllers\api\ProjectManagerController;
use app\controllers\api\ProjectRecordController;
use app\controllers\api\RoleController;
use app\controllers\api\UserController;


require_once(__DIR__ . "../../vendor/autoload.php");


$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();


$config = [
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
    ]
];
$app = new Application($config);


$app->router->get('/api/emailvalidate?', [MemberController::class, 'emailvalidate']); //信箱驗證
$app->router->post('/api/login', [LoginController::class, 'login']); //登入
$app->router->post('/api/forgetPassword', [MemberController::class, 'forgetPassword']); //發送重設密碼請求
$app->router->post('/api/resetCodeValidate', [MemberController::class, 'resetCodeValidate']); //重設密碼驗證
$app->router->post('/api/resetPassword', [MemberController::class, 'resetPassword']); //重設密碼


$app->router->get('/api/member', [MemberController::class, 'index']); //取得所有會員公開資料
$app->router->get('/api/member?', [MemberController::class, 'show']); //取得該會員公開資料
$app->router->get('/api/member/game', [MemberController::class, 'getSelfGameRecord']); //取得自己競賽記錄列表
$app->router->put('/api/member/pwd', [MemberController::class, 'updatePassword']); //會員更改密碼
$app->router->put('/api/member/info', [MemberController::class, 'updateIntroduction']); //會員更改個人簡介
$app->router->put('/api/member/photo', [MemberController::class, 'updateMemberPhoto']); //會員更改個人大頭貼



$app->router->get('/api/manager/permission', [PermissionController::class, 'index']); //取得所有權限資料

$app->router->get('/api/manager/role/select', [RoleController::class, 'selector']); //取得所有專案性質(下拉式)
$app->router->get('/api/manager/role/list', [RoleController::class, 'index']); //取得所有角色資料
$app->router->get('/api/manager/role/list?', [RoleController::class, 'index']); //取得所有角色資料(搜尋)
$app->router->get('/api/manager/role?', [RoleController::class, 'show']); //取得該角色權限
$app->router->get('/api/manager/role/user?', [RoleController::class, 'getMember_Role']); //取得該帳號角色
$app->router->post('/api/manager/role', [RoleController::class, 'store']); //新增角色權限
$app->router->put('/api/manager/role/user', [RoleController::class, 'updateMemberRole']); //修改使用者角色
$app->router->put('/api/manager/role', [RoleController::class, 'update']); //修改角色權限
$app->router->delete('/api/manager/role?', [RoleController::class, 'destroy']); //刪除角色資料

$app->router->get('/api/manager/user', [UserController::class, 'index']); //取得所有會員資料
$app->router->get('/api/manager/user?', [UserController::class, 'index']); //取得所有會員資料
$app->router->post('/api/manager/useradd', [UserController::class, 'useradd']); //加入學生
$app->router->put('/api/manager/user/password', [UserController::class, 'changeUserPassword']); //修改使用者密碼
$app->router->put('/api/manager/user/class', [UserController::class, 'updateUserClass']); //修改使用者班級
$app->router->delete('/api/manager/user?', [UserController::class, 'destroy']); //刪除使用者

$app->router->get('/api/manager/teacher/list', [UserController::class, 'getAllTeacher']); //取得所有教師
$app->router->get('/api/manager/teacher/list?', [UserController::class, 'getAllTeacher']); //取得教師(搜尋值)
$app->router->get('/api/manager/teacher?', [UserController::class, 'getTeacher']); //取得教師資料
$app->router->post('/api/manager/teacher', [UserController::class, 'teacheradd']); //新增教師
$app->router->put('/api/manager/teacher/info', [UserController::class, 'updateTeacherInfo']); //修改教師資訊
$app->router->put('/api/manager/teacher/photo', [UserController::class, 'updateTeacherPhoto']); //修改教師大頭貼
$app->router->delete('/api/manager/teacher?', [UserController::class, 'destroyTeacher']); //刪除教師

$app->router->get('/api/academic', [AcademicController::class, 'index']); //取得所有學制
$app->router->get('/api/academic?', [AcademicController::class, 'show']); //取得該學制班級
$app->router->post('/api/academic', [AcademicController::class, 'store']); //新增學制
$app->router->put('/api/academic', [AcademicController::class, 'update']); //修改學制
$app->router->delete('/api/academic?', [AcademicController::class, 'destroy']); //刪除學制

$app->router->get('/api/class', [ClassesController::class, 'index']); //取得所有班級
$app->router->get('/api/class?', [ClassesController::class, 'show']); //取得該班級學生
$app->router->post('/api/class', [ClassesController::class, 'store']); //新增班級
$app->router->put('/api/class', [ClassesController::class, 'update']); //修改班級
$app->router->delete('/api/class?', [ClassesController::class, 'destroy']); //刪除班級

$app->router->get('/api/meeting/list', [MeetController::class, 'index']); //取得會議列表
$app->router->get('/api/meeting/list?', [MeetController::class, 'index']); //取得會議列表(搜尋值)
$app->router->get('/api/meeting?', [MeetController::class, 'show']); //取得該會議記錄
$app->router->post('/api/meeting', [MeetController::class, 'store']); //新增會議記錄
$app->router->put('/api/meeting', [MeetController::class, 'update']); //修改會議記錄
$app->router->delete('/api/meeting?', [MeetController::class, 'destroy']); //刪除會議記錄(軟刪除)
$app->router->get('/api/download/meeting?', [DownloadController::class, 'download_Meet']); //下載會議記錄檔案

$app->router->get('/api/game/type', [GameManagerController::class, 'index']); //取得所有競賽類別
$app->router->get('/api/game/type?', [GameManagerController::class, 'show']); //取得該競賽類別所有記錄
$app->router->post('/api/game/type', [GameManagerController::class, 'store']); //新增競賽類別
$app->router->put('/api/game/type', [GameManagerController::class, 'update']); //修改競賽類別
$app->router->delete('/api/game/type?', [GameManagerController::class, 'destroy']); //刪除競賽類別

$app->router->get('/api/game', [GameRecordController::class, 'index']); //取得所有競賽記錄列表
$app->router->get('/api/game?', [GameRecordController::class, 'show']); //取得該競賽記錄
$app->router->post('/api/game', [GameRecordController::class, 'store']); //新增競賽記錄
$app->router->put('/api/game', [GameRecordController::class, 'update']); //修改競賽記錄
$app->router->delete('/api/game?', [GameRecordController::class, 'destroy']); //刪除競賽記錄(軟刪除)
$app->router->get('/api/download/game?', [DownloadController::class, 'download_Game']); //下載競賽記錄檔案

$app->router->get('/api/project/type/select', [ProjectManagerController::class, 'selector']); //取得所有專案性質(下拉式)
$app->router->get('/api/project/type', [ProjectManagerController::class, 'index']); //取得所有專案性質
$app->router->get('/api/project/type?', [ProjectManagerController::class, 'index']); //取得所有專案性質(搜尋值)
$app->router->get('/api/project/list', [ProjectManagerController::class, 'show']); //取得所有專案
$app->router->get('/api/project/list?', [ProjectManagerController::class, 'show']); //取得該專案性質所有專案(搜尋值)
$app->router->post('/api/project/type', [ProjectManagerController::class, 'store']); //新增專案性質
$app->router->put('/api/project/type', [ProjectManagerController::class, 'update']); //修改專案性質
$app->router->delete('/api/project/type?', [ProjectManagerController::class, 'destroy']); //刪除專案性質

$app->router->get('/api/project?', [ProjectRecordController::class, 'index']); //取得該專案所有資訊(含記錄)(搜尋值)
$app->router->get('/api/project/record?', [ProjectRecordController::class, 'show']); //取得該專案所有記錄(搜尋值)
$app->router->post('/api/project', [ProjectRecordController::class, 'store']); //新增專案
$app->router->post('/api/project/record', [ProjectRecordController::class, 'storeRecord']); //新增專案記錄
$app->router->put('/api/project', [ProjectRecordController::class, 'update']); //修改專案
$app->router->put('/api/project/record', [ProjectRecordController::class, 'updateRecord']); //修改專案記錄
$app->router->delete('/api/project?', [ProjectRecordController::class, 'destroy']); //刪除專案(軟刪除)
$app->router->delete('/api/project/record?', [ProjectRecordController::class, 'destroyRecord']); //刪除專案記錄(軟刪除)
$app->router->get('/api/download/project?', [DownloadController::class, 'download_Project']); //下載專案記錄檔案

$app->router->get('/api/labinfo/list', [LabInfoController::class, 'index']); //取得所有研究室介紹
$app->router->get('/api/labinfo/list?', [LabInfoController::class, 'index']); //取得所有研究室介紹(搜尋值)
$app->router->get('/api/labinfo?', [LabInfoController::class, 'show']); //取得該研究室介紹內容
$app->router->post('/api/labinfo', [LabInfoController::class, 'store']); //新增研究室介紹
$app->router->put('/api/labinfo', [LabInfoController::class, 'update']); //修改研究室介紹
$app->router->delete('/api/labinfo?', [LabInfoController::class, 'destroy']); //刪除研究室介紹

$app->router->get('/api/album/list', [AlbumController::class, 'index']); //取得所有相簿
$app->router->get('/api/album/list?', [AlbumController::class, 'index']); //取得所有相簿(搜尋值)
$app->router->get('/api/album?', [AlbumController::class, 'show']); //取得該相簿內容
$app->router->post('/api/album', [AlbumController::class, 'store']); //新增相簿
$app->router->put('/api/album', [AlbumController::class, 'update']); //修改相簿
$app->router->delete('/api/album?', [AlbumController::class, 'destroy']); //刪除相簿

$app->router->get('/api/book/list', [BookController::class, 'index']); //取得所有出版品
$app->router->get('/api/book/list?', [BookController::class, 'index']); //取得所有出版品(搜尋值)
$app->router->get('/api/book?', [BookController::class, 'show']); //取得該出版品內容
$app->router->post('/api/book', [BookController::class, 'store']); //新增出版品
$app->router->put('/api/book', [BookController::class, 'update']); //修改出版品資訊
$app->router->put('/api/book/image', [BookController::class, 'updateImage']); //修改出版品資訊
$app->router->delete('/api/book?', [BookController::class, 'destroy']); //刪除出版品



$app->run();
