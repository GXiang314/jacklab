<?php
namespace app\publics;

use app\core\Application;
use app\controllers\api\MemberController;
// use app\core\Application;

require_once (__DIR__."../../vendor/autoload.php");



$app = new Application();

$app->router->get('/api/manager/user',[new MemberController(),'index']);

$app->router->get('/user',[]);

$app->router->post('/user',[]);

$app->run();