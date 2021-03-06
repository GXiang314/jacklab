<?php

namespace app\middlewares;

use app\core\Application;
use app\core\DbModel;
use app\core\Middleware;
use app\core\Request;
use app\core\Response;
use app\services\JwtService;

class isLoginMiddleware extends Middleware
{

    public array $actions = [];

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    public function execute(Request $request)
    {

        if (in_array(Application::$app->controller->action, $this->actions)) {
            if ($request->header('Authorization') != null) {
                $bearer = explode(' ', $request->header('Authorization'));
                $token = $bearer[1];
                $jwt = new JwtService();
                $result = $jwt->Jwt_user_decode($token);
                if ($result != 'error') {
                    $data = DbModel::findOne('member', ['Account' => $result['account']]);
                    $roledata = DbModel::findOne('member_role', [
                        'Account' => $result['account'],
                        'Role_Id' => $result['roles'],
                    ]);
                    if (!empty($data) && strtotime(date('Y-m-d h:i:s')) - $result['exp'] < 0) {
                        $request->addKeys([
                            'ROLE' => $roledata['Role_Id'] ?? null,
                            'USER' => $result['account'],
                            'TOKEN' => $token,
                            'ADMIN' => $data['IsAdmin'] ?? false
                        ]);
                        return $request;
                    }
                }
            }
            echo Response::json([
                'success' => false,
                'message' => '請先登入！',
            ], 401);
            exit;
        }
        return $request;
    }
}
