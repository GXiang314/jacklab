<?php
namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\services\MemberService;

class MemberController extends Controller{
    private $memberService;
    public function __construct()
    {
        $this->memberService = new MemberService();
        
    }
    /**
     * Get all resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->memberService->getAllMember();
        return ($data !=[])? $this->sendResponse($data,'所有成員'):$this->sendError('沒有資料');
    }

     /**
     * Get one resource in storage.
     *
     * @param  \app\core\Request
     * @return \app\core\Response
     */
    public function show(Request $request)
    {        
        if($request->isGet()){
            $sid = $request->getBody()['id'];
            $data = $this->memberService->getMemberData($sid);
        }        
        return ($data !=[])? $this->sendResponse($data,$sid):$this->sendError('沒有資料');        
    }
    
    /**
     * Update password with member in storage.
     *
     * @param  \app\core\Request
     * @return \app\core\Response
     *//*
    public function updatePassword(Request $request)
    {
        try {
            $request->validate([ //這邊會驗證註冊的資料是否符合格式
                'USER' => 'required',
                'oldpassword' => ['required', 'string'],
                'password' => ['required', 'string'],
                'password_confirmation' => ['required', 'string','same:password'],
            ]);
            $result = $this->memberService->updatePassword($request['USER'],$request['oldpassword'],$request['password']);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 'Updating failed.');
        }
        if($result=='success'){
            return $this->sendResponse($result,'success');
        }
        return $this->sendError($result);
    }

    /**
     * Update data in storage.
     *
     * @param  \app\core\Request
     * @return \app\core\Response
     *//*
    public function updateIntroduction(Request $request)
    {
        try{
            $request->validate([
                'USER' =>'required'
            ]);
            if(isset($request['text'])){
                $result = $this->memberService->updateIntroduction($request['USER'],$request['text']);
                if($result == 'success'){
                    return $this->sendResponse($result,'success');
                }
            }
        }catch(Exception $e){
            return $this->sendError('Updating failed');
        }
        return $this->sendError($result);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \app\core\Request
     * @return \app\core\Response
     *//*
    public function emailvalidate(string $account,string $token)
    {
        $result = $this->memberService->emailTokenCheck($account,$token);
        if($result == 'success'){
            return $this->sendResponse($result,'信箱驗證成功');
        }else{
            return $this->sendError($result,'信箱驗證失敗，請聯絡系統管理員');
        }
    }*/
}





