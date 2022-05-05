<?php
namespace app\publics;

use app\core\Application;
use app\controllers\api\MemberController;
use app\controllers\api\UserController;

// use app\core\Application;

require_once (__DIR__."../../vendor/autoload.php");



$app = new Application();

$app->router->get('/api/manager/user',[MemberController::class,'index']);
$app->router->post('/api/manager/useradd',[UserController::class,'useradd']);

$app->router->get('/user',[]);

$app->router->post('/user',[]);

$app->run();