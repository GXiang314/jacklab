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
            if ($request->getBody()['ADMIN'] ?? false) return $request;
            $role = $request->getBody()['ROLE'];
            $className = pathinfo(Application::$app->controller::class, PATHINFO_FILENAME);

            $nowUrl = $className . "@" . Application::$app->controller->action;
            $statement = DbModel::prepare("
            SELECT
                p.Url 
            FROM
                role AS r,
                role_permission_group AS rp,
                permission_group as pg,
                permission AS p 
            WHERE
                r.Id = rp.Role_Id 
                AND rp.Permission_group = pg.Id
                AND p.Permission_group = pg.Id
                AND r.Id = '{$role}';
            ");
            $statement->execute();
            $userPrm = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement = null;
            $userPrmUrl = [];
            foreach ($userPrm as $key => $val) {
                $userPrmUrl[] = $val['Url'];
            }
            // in_array($nowUrl,$userPrmUrl)
            if (in_array($nowUrl, $userPrmUrl)) {
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
