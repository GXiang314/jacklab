<?php
namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\model\Useradd;
use app\services\MemberService;
use Exception;

class UserController extends Controller{

    private $memberService;
    public function __construct()
    {
        $this->memberService = new MemberService();
        
    }


    public function index()
    {
        $data = $this->memberService->getAllMember();
        return ($data !=[])? $this->sendResponse($data,'所有成員'):$this->sendError('沒有資料');
    }

    public function useradd(Request $request)
    {
        try {
            $userAddModel = new Useradd();

            if($request->isPost()){
                $userAddModel->loadData($request->getJson());
                if($userAddModel->validate()){

                }else{
                    var_dump($userAddModel->errors) ;
                }
               
            }
            // $request['token'] = $this->memberService->generateAuthToken();
            // $request['password'] = $this->memberService->generatePassword();
            // $result = $this->memberService->studentAdd($request);
            // $url = action([MemberController::class,'emailvalidate'],['account' => $request['account'],'token' => $request['token']]);
            // $content = "使用者「{$request['name']}」，您好：\r\n\r\n您的帳號是：{$request['account']}\r\n您的密碼是：{$request['password']}\r\n\r\n請點擊以下連結以完成驗證步驟：\r\n{$url}";
            // Mail::raw($content, function ($message) use ($request) {
            //     $message
            //     ->to($request['account'])
            //     ->subject("創建帳號通知");
            // });
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 'Registered failed.');
        }
        return $this->sendResponse($userAddModel,'success');
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
    }

    public function changeUserPassword(Request $request)
    {
        try {

            $result = $this->memberService->updateUserPassword($request['account'],$request['password']);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 'Updating failed.');
        }
        if($result=='success'){
            return $this->sendResponse($result,'success');
        }
        return $this->sendError($result);
    }


    public function show($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($student_id)
    {
        $result = $this->memberService->delete($student_id);
        if($result == 'success'){
            return $this->sendResponse($result,'刪除成功');
        }
        return $this->sendError($result,'刪除失敗，請稍後再試');
    }*/
}