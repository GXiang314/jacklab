<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\DbModel;
use app\core\Request;
use app\middlewares\isLoginMiddleware;
use app\requestModel\ChangePassword;
use app\requestModel\EmailValidate;
use app\requestModel\ResetPassword;
use app\requestModel\UpdateMemberPhoto;
use app\services\MailService;
use app\services\MemberService;
use app\core\Exception\MethodNotAllowException;

class MemberController extends Controller
{
    private $memberService;
    private $mailService;
    public function __construct()
    {
        $this->memberService = new MemberService();
        $this->mailService = new MailService();
        $this->registerMiddleware(new isLoginMiddleware(['index', 'show', 'getSelf', 'getSelfProject', 'updatePassword', 'updateIntroduction', 'updateMemberPhoto', 'getSelfProject']));
    }

    public function getPastStudent(Request $request)
    {
        if($request->isGet()){
            $time = $request->getBody()['time'] ?? '%';
            if(empty($time)) $time = '%';
            $data = $this->memberService->getStudent($time);
            return $data ? $this->sendResponse($data, '成員列表') : $this->sendResponse('', '沒有資料');
        }
        throw new MethodNotAllowException();
    }


    /**
     * Get all resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->memberService->getPublicAllMember();
        return ($data != []) ? $this->sendResponse($data, '所有成員') : $this->sendResponse('', '沒有資料');
    }

    /**
     * Get student all project record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSelf(Request $request)
    {
        if ($request->isGet()) {
            $account = $request->getBody()['USER'];
            $data = $this->memberService->getPublicAccountMember($account);
            return ($data != []) ? $this->sendResponse($data, '參與專案記錄') : $this->sendResponse('', '沒有資料');
        }
        throw new MethodNotAllowException();
    }

    /**
     * Get student all project record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSelfProject(Request $request)
    {
        if ($request->isGet()) {
            $account = $request->getBody()['USER'];
            $page = $request->getBody()['page'] ?? 1;
            $page = (!is_numeric($page)) ? 1 : intval($page);
            $data = $this->memberService->getSelfProject($account, $page);
            return ($data != []) ? $this->sendResponse($data, '參與專案記錄') : $this->sendResponse('', '沒有資料');
        }
        throw new MethodNotAllowException();
    }

    // /**
    //  * Get student all game record in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function getSelfGameRecord(Request $request)
    // {
    //     if ($request->isGet()) {
    //         $account = $request->getBody()['USER'];
    //         $data = $this->memberService->getStudentGameRecord($account);
    //         return ($data != []) ? $this->sendResponse($data, '所有競賽記錄') : $this->sendResponse('', '沒有資料');
    //     }
    //     throw new MethodNotAllowException();
    // }

    /**
     * Get one resource in storage.
     *
     * @param  \app\core\Request
     * @return \app\core\Response
     */
    public function show(Request $request)
    {
        if ($request->isGet()) {
            $sid = $request->getBody()['id'] ?? 0;
            $data = $this->memberService->getPublicMember($sid);
        }
        return ($data != []) ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
    }

    /**
     * Update password with member in storage.
     *
     * @param  \app\core\Request
     * @return \app\core\Response
     */
    public function updatePassword(Request $request)
    {
        if ($request->isPut()) {
            $requestModel = new ChangePassword();
            $data = $request->getbody();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->memberService->updatePassword($data['USER'], $requestModel->oldpassword, $requestModel->password);
                return $result == 'success' ? $this->sendResponse($result, '修改成功') : $this->sendError($result);
            }
            return $this->sendError($requestModel->getFirstError());
        }
        throw new MethodNotAllowException();
    }

    /**
     * Update data in storage.
     *
     * @param  \app\core\Request
     * @return \app\core\Response
     */
    public function updateIntroduction(Request $request)
    {
        if ($request->isPut()) {
            $data = $request->getbody();
            $result = $this->memberService->updateIntroduction($data['USER'], $data['text']);
            return $result == 'success' ? $this->sendResponse($result, '修改成功') : $this->sendError($result);
        }
        throw new MethodNotAllowException();
    }

    /**
     * Update data in storage.
     *
     * @param  \app\core\Request
     * @return \app\core\Response
     */
    public function updateMemberPhoto(Request $request)
    {
        if ($request->isPut()) {
            $data = $request->getBody();
            $requestModel = new UpdateMemberPhoto();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->memberService->updatePhoto($requestModel->USER, $requestModel->File);
                return $result == 'success' ? $this->sendResponse($result, '變更成功') : $this->sendError($result);
            }
            return $this->sendError($requestModel->getFirstError());
        }
        throw new MethodNotAllowException();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \app\core\Request
     * @return \app\core\Response
     */
    public function emailvalidate(Request $request)
    {
        if ($request->isGet()) {
            $data = $request->getBody();
            $requestModel = new EmailValidate();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->memberService->emailTokenCheck($data['email'], $data['token']);
                return $result == 'success' ? $this->sendResponse($result, '信箱驗證成功') : $this->sendError($result);
            } else {
                return $this->sendError('傳送資料錯誤');
            }
        }
        throw new MethodNotAllowException();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \app\core\Request
     * @return \app\core\Response
     */
    public function forgetPassword(Request $request)
    {
        if ($request->isPost()) {
            $account = $request->getbody()['Account'] ?? '';
            $data = $this->memberService->getAccountData($account);
            if (!empty($data)) {
                $checkdata = DbModel::findOne('reset_password', [
                    'Account' => $data['Account'],
                ]);
                if ($checkdata) {
                    if (strtotime($checkdata['Update_at']) - strtotime(time()) < 1740) {
                        return $this->sendError('請稍後再試');
                    }
                }
                $code = $this->memberService->generateAuthToken(6);
                $this->mailService->sendForgetPasswordMail($data['Name'], $data['Account'], $code);
                DbModel::create('reset_password', [
                    'Account' => $data['Account'],
                    'Update_at' => date('Y-m-d h:i:s', time() + 1800),
                    'Code' => $code
                ]);

                return $this->sendResponse("success", "已發送驗證碼至您的信箱，請查收");
            }
            return $this->sendError('無此會員帳號');
        }
        throw new MethodNotAllowException();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \app\core\Request
     * @return \app\core\Response
     */
    public function resetCodeValidate(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->getbody() ?? '';
            if (!empty($data)) {
                $checkdata = DbModel::findOne('reset_password', [
                    'Account' => $data['Account'],
                    'Code' => $data['Code'],
                ]);
                if (!empty($checkdata)) {
                    if (strtotime($checkdata['Update_at']) > strtotime(time())) {
                        return $this->sendResponse('success', '驗證成功');
                    }
                    return $this->sendError('此驗證碼已過期');
                }
                return $this->sendError('驗證碼錯誤');
            }
            return $this->sendError('驗證失敗');
        }
        throw new MethodNotAllowException();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \app\core\Request
     * @return \app\core\Response
     */
    public function resetPassword(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->getbody() ?? '';
            $checkdata = DbModel::findOne('reset_password', [
                'Account' => $data['account'],
                'Code' => $data['code'],
            ]);
            if (!empty($checkdata)) {
                if (strtotime($checkdata['Update_at']) > strtotime(time())) {
                    $requestModel = new ResetPassword();
                    $requestModel->loadData($data);
                    if ($requestModel->validate()) {
                        $res = $this->memberService->updateUserPassword($requestModel->account, $requestModel->password);
                        DbModel::delete('reset_password', [
                            'Account' => $requestModel->account,
                        ]);
                        return $res ? $this->sendResponse($res, "修改成功！") : $this->sendError("修改失敗");
                    }
                    return $this->sendError('兩次密碼輸入不一致');
                }
                DbModel::delete('reset_password', [
                    'Account' => $data['Account'],
                    'Code' => $data['Code']
                ]);
                return $this->sendError('請重新驗證');
            }
        }
        throw new MethodNotAllowException();
    }
}
