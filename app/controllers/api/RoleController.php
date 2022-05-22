<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\middlewares\hasRoleMiddleware;
use app\middlewares\isLoginMiddleware;
use app\requestModel\AddRole;
use app\requestModel\UpdateMemberRole;
use app\requestModel\UpdateRole;
use app\services\RoleService;

class RoleController extends Controller
{

    private $roleService;
    public function __construct()
    {
        $this->roleService = new RoleService();
        $this->registerMiddleware(new isLoginMiddleware(['index', 'show', 'getMember_Role', 'store', 'update', 'updateMemberRole', 'destroy']));
        $this->registerMiddleware(new hasRoleMiddleware(['index', 'show', 'getMember_Role', 'store', 'update', 'updateMemberRole', 'destroy']));
    }

    public function selector()
    {
        $data = $this->roleService->getAllNoPaging();
        return $data ? $this->sendResponse($data, '所有角色資料') : $this->sendResponse('', '沒有資料');
    }

    public function index(Request $request)
    {
        if ($request->isGet()) {
            $page = $request->getBody()['page'] ?? 1;
            $page = (!is_numeric($page)) ? 1 : intval($page);
            $search = $request->getBody()['search'] ?? null;
            $data = $this->roleService->getAll($page, $search);
            return $data ? $this->sendResponse($data, '所有角色資料') : $this->sendResponse('', '沒有資料');
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function show(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->getBody()['id'] ?? '';
            $data = $this->roleService->getRole_Permission($id);
            return (!empty($data)) ? $this->sendResponse($data, '該角色的權限') : $this->sendResponse('', '沒有資料');
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function getMember_Role(Request $request)
    {
        if ($request->isGet()) {
            $account = $request->getBody()['account'] ?? '';
            $data = $this->roleService->getMember_Role($account);
            return (!empty($data)) ? $this->sendResponse($data, '該使用者的角色') : $this->sendResponse('', '沒有資料');
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function store(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->getJson();
            $requestModel = new AddRole();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->roleService->add($data['Name'], $data['Permission']);
                return $result == 'success' ? $this->sendResponse($result, '新增成功') : $this->sendError('新增失敗', $result);
            } else {
                return $this->sendError('欄位格式錯誤', $requestModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function update(Request $request)
    {
        if ($request->isPut()) {
            $data = $request->getJson();
            $requestModel = new UpdateRole();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->roleService->update($data['Id'], $data['Permission']);
                return $result == 'success' ? $this->sendResponse($result, '修改成功') : $this->sendError('修改失敗', $result);
            } else {
                return $this->sendError('欄位格式錯誤', $requestModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function updateMemberRole(Request $request)
    {
        if ($request->isPut()) {
            $data = $request->getJson();
            $requestModel = new UpdateMemberRole();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->roleService->updateMemberRole($data['Account'], $data['Role']);
                return $result == 'success' ? $this->sendResponse($result, '修改成功') : $this->sendError('修改失敗', $result);
            } else {
                return $this->sendError('欄位格式錯誤', $requestModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function destroy(Request $request)
    {
        if ($request->isDelete()) {
            $id = $request->getBody()['id'] ?? '';
            $result = $this->roleService->delete($id);
            return $result == 'success' ? $this->sendResponse($result, '刪除成功') : $this->sendError('刪除失敗', $result);
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }
}
