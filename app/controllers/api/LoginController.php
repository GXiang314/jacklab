<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\middlewares\isLoginMiddleware;
use app\requestModel\Login;
use app\services\JwtService;
use app\services\MemberService;

class LoginController extends Controller
{
    private $memberService;
    private $jwtService;
    public function __construct()
    {
        $this->memberService = new MemberService();
        $this->jwtService = new JwtService();
        $this->registerMiddleware(new isLoginMiddleware(['isLogin']));
    }

    public function isLogin(Request $request)
    {
        $data = $request->getBody();
        if($data['USER'] ?? false){            
            $res['token'] = $data['TOKEN'];
            $res['admin'] = $data['ADMIN'];
            $res['role'] = $data['ROLE'];
            $res['account'] = $data['USER'];
            return $this->sendResponse($res, "登入成功");
        }else{
            return $this->sendError("請先登入", [], 401);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \app\core\Request
     * @return \app\core\Response
     */
    public function login(Request $request)
    {        
        if ($request->isPost()) {
            $requestModel = new Login();
            $data = $request->getJson();
            $requestModel->loadData($data);

            if ($requestModel->validate()) {
                if ($this->memberService->passwordCheck($data['Account'], $data['Password'])) {
                    if ($this->memberService->isEmailValidate($data['Account'])) {
                        $member = $this->memberService->getAccount($data['Account']);
                        $res['token'] = $this->jwtService->Jwt_user_encode($member['Account'], $member['Role']);
                        $res['admin'] = $member['IsAdmin'];
                        $res['role'] = $member['Role']['Id'] ?? null;
                        $res['account'] = $member['Account'];
                        return $this->sendResponse($res, "登入成功");
                    } else {
                        return $this->sendError('信箱未完成驗證，請查看信箱', [], 401);
                    }
                } else {
                    return $this->sendError('帳號或密碼輸入錯誤');
                }
            } else {
                return $this->sendError($requestModel->getFirstError());
            }
        }
        return $this->sendError("Method Not Allow.", [], 405);
    }
}
