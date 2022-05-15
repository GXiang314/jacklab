<?php

namespace app\controllers\api;

use app\core\Controller;
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
        $this->registerMiddleware(new isLoginMiddleware(['index', 'useradd', 'changeUserPassword', 'updateUserClass', 'destroy']));
        $this->registerMiddleware(new hasRoleMiddleware(['index', 'useradd', 'changeUserPassword', 'updateUserClass', 'destroy']));
        // $this->registerMiddleware(new hasRoleMiddleware(['index', 'useradd', 'destroy']));
    }


    public function index(Request $request)
    {
        if ($request->isGet()) {
            $page = $request->getBody()['page'] ?? 1;
            $search = $request->getBody()['search'] ?? null;
            $academic = $request->getBody()['academic'] ?? null;
        }
        $data = $this->memberService->getAllMember($page, $search, $academic);
        return ($data != []) ? $this->sendResponse($data, '所有成員') : $this->sendResponse('', '沒有資料');
    }

    public function getAllTeacher(Request $request)
    {
        if ($request->isGet()) {
            $page = $request->getBody()['page'] ?? 1;
            $search = $request->getBody()['search'] ?? null;
        }
        $data = $this->memberService->getTeacher($page, $search);
        return $data ? $this->sendResponse($data, '所有教師') : $this->sendResponse('', '沒有資料');
    }

    public function useradd(Request $request)
    {
        if ($request->isPost()) {
            $userAddModel = new Useradd();
            $data = $request->getJson();
            $userAddModel->loadData($data);
            if ($userAddModel->validate()) {
                $res = $this->memberService->studentAdd($data);
                return $res? $this->sendResponse($res, '加入成功') : $this->sendError($res, '加入失敗');
            } else {
                return $this->sendError($userAddModel->errors, 'Registered failed.');
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function teacheradd(Request $request)
    {
        if ($request->isPost()) {
            $teachererAddModel = new Teacheradd();
            $data = $request->getJson();
            $teachererAddModel->loadData($data);
            if ($teachererAddModel->validate()) {
                $res = $this->memberService->teacherAdd($data);
                return $res? $this->sendResponse($res, '加入成功') : $this->sendError($res, '加入失敗');
            } else {
                return $this->sendError($teachererAddModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function updateTeacherInfo(Request $request)
    {
        if ($request->isPut()) {
            $teachererAddModel = new UpdateTeacherInfo();
            $data = $request->getJson();
            $teachererAddModel->loadData($data);
            if ($teachererAddModel->validate()) {
                $res = $this->memberService->updateTeacherInfo($teachererAddModel->Id, $data);
                return $res == 'success' ? $this->sendResponse($res, '修改成功') : $this->sendError($res, '修改失敗');
            } else {
                return $this->sendError($teachererAddModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function updateTeacherPhoto(Request $request)
    {
        if ($request->isPut()) {
            $data = $request->getBody();
            $requestModel = new UpdateTeacherPhoto();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->memberService->updateTeacherPhoto($requestModel->Id, $requestModel->File);
                return $result == 'success' ? $this->sendResponse($result, 'success') : $this->sendError($result);
            }
            return $this->sendError($requestModel->errors);
        }
        return $this->sendError('Method Not Allow', [], 405);
    }

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

    public function destroyTeacher(Request $request)
    {
        if ($request->isDelete()) {
            $result = $this->memberService->deleteTeacher($request->getBody()['id'] ?? '0');
            if ($result == 'success') {
                return $this->sendResponse($result, '刪除成功');
            }
        }

        return $this->sendError($result, '刪除失敗，請稍後再試');
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
