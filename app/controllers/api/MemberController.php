<?php
namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\requestModel\ChangePassword;
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
            $sid = $request->getBody()['id'] ?? 0;
            $data = $this->memberService->getPublicMember($sid);
        }        
        return ($data !=[])? $this->sendResponse($data,'success'):$this->sendError('沒有資料');        
    }
    
    /**
     * Update password with member in storage.
     *
     * @param  \app\core\Request
     * @return \app\core\Response
     */
    public function updatePassword(Request $request)
    {

        if($request->isPut()){
            $requestModel = new ChangePassword();
            $data = $request->getJson();
            $requestModel->loadData($data);
            if($requestModel->validate()){
                $result = $this->memberService->updatePassword($data['USER'],$requestModel->old,$requestModel->new);
            }            
        }
        return $result=='success' ? $this->sendResponse($result,'success') : $this->sendError($result);
    }

    /**
     * Update data in storage.
     *
     * @param  \app\core\Request
     * @return \app\core\Response
     */
    public function updateIntroduction(Request $request)
    {
        if($request->isPut()){
            $data = $request->getJson();
            $result = $this->memberService->updateIntroduction($data['USER'],$data['text']);            
        }
        return $result=='success' ? $this->sendResponse($result,'success') : $this->sendError($result);       
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \app\core\Request
     * @return \app\core\Response
     */
    public function emailvalidate(Request $request)
    {
        if($request->isGet()){
            $data = $request->getBody();
            $result = $this->memberService->emailTokenCheck($data['email'],$data['token']);
        }
        return $result=='success' ? $this->sendResponse($result,'success') : $this->sendError($result);
    }
}





