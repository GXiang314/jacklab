<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Exception\MethodNotAllowException;
use app\core\Request;
use app\middlewares\hasRoleMiddleware;
use app\middlewares\isLoginMiddleware;
use app\requestModel\Teacheradd;
use app\requestModel\UpdateStudentClass;
use app\requestModel\UpdateTeacherInfo;
use app\requestModel\UpdateTeacherPhoto;
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
        $this->registerMiddleware(new isLoginMiddleware(['index', 'useradd', 'teacheradd', 'updateTeacherInfo', 'changeUserPassword', 'updateUserClass', 'destroy', 'destroyTeacher']));
        $this->registerMiddleware(new hasRoleMiddleware(['index', 'useradd', 'teacheradd', 'updateTeacherInfo', 'changeUserPassword', 'updateUserClass', 'destroy', 'destroyTeacher']));
        // $this->registerMiddleware(new hasRoleMiddleware(['index', 'useradd', 'destroy']));
    }


    public function index(Request $request)
    {
        if ($request->isGet()) {
            $page = $request->getBody()['page'] ?? 1;
            $page = (!is_numeric($page)) ? 1 : intval($page);
            $search = $request->getBody()['search'] ?? '';
            $search = (empty(trim($search))) ? null : $search;
            $academic = $request->getBody()['academic'] ?? null;
            $academic = (empty(trim($academic))) ? null : $academic;
            $data = $this->memberService->getAllMember($page, $search, $academic);
            return ($data != []) ? $this->sendResponse($data, '所有成員') : $this->sendResponse('', '沒有資料');
        }
        throw new MethodNotAllowException();        
    }

    public function getAllTeacher(Request $request)
    {
        if ($request->isGet()) {
            $page = $request->getBody()['page'] ?? 1;
            $page = (!is_numeric($page)) ? 1 : intval($page);
            $search = $request->getBody()['search'] ?? null;
            $data = $this->memberService->getTeacher($page, $search);
            return $data ? $this->sendResponse($data, '所有教師') : $this->sendResponse('', '沒有資料');
        }
        throw new MethodNotAllowException();        
    }

    public function getTeacher(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->getBody()['id'] ?? '';
            $data = $this->memberService->getTeacherData($id);
            return $data ? $this->sendResponse($data, '所有教師') : $this->sendResponse('', '沒有資料');
        }
        throw new MethodNotAllowException();        
    }

    public function useradd(Request $request)
    {
        if ($request->isPost()) {
            $userAddModel = new Useradd();
            $data = $request->getbody();
            $userAddModel->loadData($data);
            if ($userAddModel->validate()) {
                $res = $this->memberService->studentAdd($data);
                return $res ? $this->sendResponse($res, '加入成功') : $this->sendError($res);
            } else {
                return $this->sendError($userAddModel->getFirstError());
            }
        }
        throw new MethodNotAllowException();
    }

    public function teacheradd(Request $request)
    {
        if ($request->isPost()) {
            $teachererAddModel = new Teacheradd();
            $data = $request->getbody();
            $teachererAddModel->loadData($data);
            if ($teachererAddModel->validate()) {
                $res = $this->memberService->teacherAdd($teachererAddModel);
                return $res ? $this->sendResponse($res, '加入成功') : $this->sendError($res);
            } else {
                return $this->sendError($teachererAddModel->getFirstError());
            }
        }
        throw new MethodNotAllowException();
    }

    public function updateTeacherInfo(Request $request)
    {
        if ($request->isPut()) {
            $teachererAddModel = new UpdateTeacherInfo();
            $data = $request->getbody();
            $teachererAddModel->loadData($data);
            if ($teachererAddModel->validate()) {
                $res = $this->memberService->updateTeacherInfo($teachererAddModel->Id, $data);
                return $res == 'success' ? $this->sendResponse($res, '修改成功') : $this->sendError($res);
            } else {
                return $this->sendError($teachererAddModel->getFirstError());
            }
        }
        throw new MethodNotAllowException();
    }

    public function updateTeacherPhoto(Request $request)
    {
        if ($request->isPut()) {
            $data = $request->getBody();
            $requestModel = new UpdateTeacherPhoto();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->memberService->updateTeacherPhoto($requestModel->Id, $requestModel->File);
                return $result == 'success' ? $this->sendResponse($result, '修改成功') : $this->sendError($result);
            }
            return $this->sendError($requestModel->getFirstError());
        }
        throw new MethodNotAllowException();
    }

    public function changeUserPassword(Request $request)
    {
        if ($request->isPut()) {
            $data = $request->getbody();
            $requestModel = new UpdateUserPassword();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->memberService->updateUserPassword($requestModel->account, $requestModel->password);
                return ($result == 'success') ? $this->sendResponse($result, 'success') : $this->sendError($result);
            }
            return $this->sendError($requestModel->getFirstError());
        }
        throw new MethodNotAllowException();
    }

    public function updateUserClass(Request $request)
    {
        if ($request->isPut()) {
            $data = $request->getbody();
            $requestModel = new UpdateStudentClass();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->memberService->updateStudentClass($requestModel->Account, $requestModel->Class);
                return ($result == 'success') ? $this->sendResponse($result, 'success') : $this->sendError($result);
            }
            return $this->sendError($requestModel->getFirstError());
        }
        throw new MethodNotAllowException();
    }

    public function destroyTeacher(Request $request)
    {
        if ($request->isDelete()) {
            $data = $request->getBody();
            $USER_ID = $this->memberService->getAccount($data['USER'])['Id'] ?? '0';
            if(!in_array($USER_ID, $data['id'] ?? [])){
                $result = $this->memberService->deleteTeacher($data['id']);
            }else{
                $result = "不可刪除自己！";
            }
            return ($result == 'success') ? $this->sendResponse($result, 'success') : $this->sendError($result);
        }
        throw new MethodNotAllowException();
    }

    public function destroy(Request $request)
    {
        if ($request->isDelete()) {
            $result = $this->memberService->delete($request->getBody()['id'] ?? '0');
            return ($result == 'success') ? $this->sendResponse($result, 'success') : $this->sendError($result);
        }
        throw new MethodNotAllowException();
    }
}
