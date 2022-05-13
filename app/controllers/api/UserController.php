<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\middlewares\hasRoleMiddleware;
use app\middlewares\isLoginMiddleware;
use app\requestModel\UpdateStudentClass;
use app\requestModel\UpdateUserPassword;
use app\requestModel\Useradd;
use app\services\MailService;
use app\services\MemberService;

class UserController extends Controller
{

    private $memberService;
    public function __construct()
    {
        $this->memberService = new MemberService();
        $this->mailService = new MailService();
        $this->registerMiddleware(new isLoginMiddleware(['index', 'useradd', 'changeUserPassword', 'updateUserClass', 'destroy']));
        $this->registerMiddleware(new hasRoleMiddleware(['index', 'useradd', 'changeUserPassword', 'updateUserClass', 'destroy']));
        // $this->registerMiddleware(new hasRoleMiddleware(['index', 'useradd', 'destroy']));
    }


    public function index(Request $request)
    {
        if($request->isGet()){
            $page = $request->getBody()['page'] ?? 1;
            $search = $request->getBody()['search'] ?? null;
            $academic = $request->getBody()['academic'] ?? null;
        }
        $data = $this->memberService->getAllMember($page, $search, $academic);
        return ($data != []) ? $this->sendResponse($data, '所有成員') : $this->sendResponse('', '沒有資料');
    }

    public function useradd(Request $request)
    {
        $userAddModel = new Useradd();
        if ($request->isPost()) {
            $data = $request->getJson();
            $userAddModel->loadData($data);
            if ($userAddModel->validate()) {
                $this->memberService->studentAdd($data);
                return $this->sendResponse([], '加入成功');
            }else{
                return $this->sendError($userAddModel->errors);
            }
        }
        return $this->sendError($userAddModel->errors, 'Registered failed.');
    }
    /*
    public function teacheradd(Request $request)
    {
        try {
            // $request->validate([ //這邊會驗證註冊的資料是否符合格式
            //     'account' => ['required', 'string', 'email', 'max:100','unique:member,Account'],
            //     'password' => ['required'],
            //     'name' => ['required', 'max:20'],
            //     'title' => ['required', 'max:20'],
            //     'role_Id' => ['required']
            // ]);
            $request['token'] = $this->memberService->generateAuthToken();
            $result = $this->memberService->addTeacher($request);
            $url = action([MemberController::class,'emailvalidate'],['account' => $request['account'],'token' => $request['token']]);
            $content = "使用者「{$request['name']}」，您好：\r\n\r\n您的帳號是：{$request['account']}\r\n您的密碼是：{$request['password']}\r\n\r\n請點擊以下連結以完成驗證步驟：\r\n{$url}";
            Mail::raw($content, function ($message) use ($request) {
                $message
                ->to($request['account'])
                ->subject("創建帳號通知");
            });
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 'Registered failed.');
        }
        return $this->sendResponse($result,'success');
    }*/

    public function changeUserPassword(Request $request)
    {
        if ($request->isPut()) {
            $data = $request->getJson();
            $requestModel = new UpdateUserPassword();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->memberService->updateUserPassword($requestModel->account, $requestModel->password);
                return ($result == 'success') ? $this->sendResponse($result, 'success') : $this->sendError($result ?? '修改失敗', [], 401);
            }
            return $this->sendError($requestModel->errors);
        }
        return $this->sendError('Method Not Allow', [], 405);
    }

    public function updateUserClass(Request $request)
    {
        if ($request->isPut()) {
            $data = $request->getJson();
            $requestModel = new UpdateStudentClass();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->memberService->updateStudentClass($requestModel->Account, $requestModel->Class);
                return ($result == 'success') ? $this->sendResponse($result, 'success') : $this->sendError($result ?? '修改失敗', [], 401);
            }
            return $this->sendError($requestModel->errors);
        }
        return $this->sendError('Method Not Allow', [], 405);
    }

    public function destroy(Request $request)
    {
        if ($request->isDelete()) {
            $result = $this->memberService->delete($request->getBody()['id'] ?? '0');
            if ($result == 'success') {
                return $this->sendResponse($result, '刪除成功');
            }
        }

        return $this->sendError($result, '刪除失敗，請稍後再試');
    }
}
