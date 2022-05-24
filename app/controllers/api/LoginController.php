<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
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
                        $res['role'] = $member['Role'];
                        return $this->sendResponse($res, "登入成功");
                    } else {
                        return $this->sendError('信箱未完成驗證，請查看信箱', [], 401);
                    }
                } else {
                    return $this->sendError('帳號或密碼輸入錯誤');
                }
            } else {
                return $this->sendError('欄位格式錯誤', $requestModel->getFirstError());
            }
        }
        return $this->sendError("Method Not Allow.", [], 405);
    }
}
