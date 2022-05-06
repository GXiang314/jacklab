<?php
namespace app\public;

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

$app->router->get('/api/manager/user',[MemberController::class,'index']);
$app->router->post('/api/manager/useradd',[UserController::class,'useradd']);

$app->router->get('/user',[]);

$app->router->post('/user',[]);

// $app->db->applyMigrations();

$app->run();