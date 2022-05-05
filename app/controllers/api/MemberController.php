<?php
namespace app\controllers\api;

use app\controllers\BaseController;
use app\services\MemberService;

class MemberController extends BaseController{
    private $memberService;
    public function __construct()
    {
        $this->memberService = new MemberService();
        
    }
    /**
     * Store a newly created resource in storage.
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show($student_id)
    {
        $data = $this->memberService->getMemberData($student_id);
        return ($data !=[])? $this->sendResponse($data,$student_id):$this->sendError('沒有資料');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
     * @param
     * @param  string $account,$authtoken
     * @return \Illuminate\Http\Response
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








?>