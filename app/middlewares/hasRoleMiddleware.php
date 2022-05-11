<?php

namespace app\middlewares;

use app\core\Application;
use app\core\DbModel;
use app\core\Middleware;
use app\core\Request;
use app\core\Response;

class hasRoleMiddleware extends Middleware
{

    public array $actions = [];

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    public function execute(Request $request)
    {
        if (in_array(Application::$app->controller->action, $this->actions)) {
            $role = $request->getBody()['ROLE'][0];
            var_dump($request->getBody()['ROLE']);
            $className = pathinfo(Application::$app->controller::class, PATHINFO_FILENAME);

            $nowUrl = $className . "@" . Application::$app->controller->action;
            $statement = DbModel::prepare("
            SELECT
                p.Url 
            FROM
                role AS r,
                role_permission AS rp,
                permission AS p 
            WHERE
                r.Id = rp.Role_Id 
                AND rp.Permission_Id = p.Id 
                AND r.Id = {$role}
            ");
            $statement->execute();
            $userPrm = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $userPrmUrl = [];
            foreach ($userPrm as $key => $val) {
                $userPrmUrl[] = $val['Url'];
            }
            // in_array($nowUrl,$userPrmUrl)
            if (in_array($nowUrl, $userPrmUrl)) {
                var_dump($role);
                var_dump($nowUrl);
                var_dump($userPrmUrl);
                return $request;
            } else {
                echo Response::json([
                    'success' => false,
                    'message' => '沒有權限！'
                ], 401);
                exit;
            }
        }
        return $request;
    }
}
