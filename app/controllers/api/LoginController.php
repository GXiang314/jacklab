<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Exception\MethodNotAllowException;
use app\core\Exception\UnauthorizedException;
use app\core\Request;
use app\middlewares\isLoginMiddleware;
use app\requestModel\Login;
use app\services\JwtService;
use app\services\MemberService;
use app\services\RoleService;
use Exception;

class LoginController extends Controller
{
    private $memberService;
    private $roleService;
    private $jwtService;
    public function __construct()
    {
        $this->memberService = new MemberService();
        $this->roleService = new RoleService();
        $this->jwtService = new JwtService();
        $this->registerMiddleware(new isLoginMiddleware(['isLogin']));
    }

    public function isLogin(Request $request)
    {
        $data = $request->getBody();
        if($data['USER'] ?? false){            
            $res['token'] = $data['TOKEN'];
            $res['admin'] = $data['ADMIN'];
            $res['permission'] = $this->roleService->getPublicRole_Permission($data['ROLE'] ?? '');
            $res['account'] = $data['USER'];
            return $this->sendResponse($res, "登入成功");
        }else{
            throw new UnauthorizedException("請先登入");
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
            $data = $request->getbody();
            $requestModel->loadData($data);

            if ($requestModel->validate()) {
                if ($this->memberService->passwordCheck($data['Account'], $data['Password'])) {
                    if ($this->memberService->isEmailValidate($data['Account'])) {
                        $member = $this->memberService->getAccount($data['Account']);
                        $res['token'] = $this->jwtService->Jwt_user_encode($member['Account'], $member['Role']);
                        $res['admin'] = $member['IsAdmin'];
                        $res['permission'] = $this->roleService->getPublicRole_Permission($member['Role']['Id'] ?? '');
                        $res['account'] = $member['Account'];
                        return $this->sendResponse($res, "登入成功");
                    } else {
                        throw new UnauthorizedException('信箱未完成驗證，請查看信箱');
                    }
                } else {
                    return $this->sendError('帳號或密碼輸入錯誤');
                }
            } else {
                return $this->sendError($requestModel->getFirstError());
            }
        }
        throw new MethodNotAllowException();
    }
}
