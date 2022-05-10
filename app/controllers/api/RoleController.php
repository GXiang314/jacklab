<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
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
    }

    public function index()
    {
        $data = $this->roleService->getAll();
        return ($data != []) ? $this->sendResponse($data, '所有角色資料') : $this->sendResponse('', '沒有資料');
    }

    public function show(Request $request)
    {
        if($request->isGet()){
            $id = $request->getBody()['id'] ?? '';
            $data = $this->roleService->getRole_Permission($id);
            return (!empty($data)) ? $this->sendResponse($data, '該角色的權限') : $this->sendResponse('', '沒有資料');
        }
        return $this->sendError('Method Not Allow.', [], 405);        
    }

    public function getMember_Role(Request $request)
    {
        if($request->isGet()){
            $account = $request->getBody()['account'] ?? '';
            $data = $this->roleService->getMember_Role($account);
            return (!empty($data)) ? $this->sendResponse($data, '該使用者的角色') : $this->sendResponse('', '沒有資料');
        }
        return $this->sendError('Method Not Allow.', [], 405);        
    }

    public function store(Request $request)
    {
        if($request->isPost()){
            $data = $request->getJson();
            $requestModel = new AddRole();
            $requestModel->loadData($data);
            if($requestModel->validate()){
                $result = $this->roleService->add($data['Name'], $data['Permission']);
                return $result=='success' ? $this->sendResponse($result, '新增成功') : $this->sendError($result, '新增失敗');
            }
        }  
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function update(Request $request)
    {
        if($request->isPut()){
            $data = $request->getJson();
            $requestModel = new UpdateRole();
            $requestModel->loadData($data);
            if($requestModel->validate()){
                $result = $this->roleService->update($data['Id'], $data['Permission']);
                return $result=='success' ? $this->sendResponse($result, '修改成功') : $this->sendError($result, '修改失敗');
            }
        }  
        return $this->sendError('Method Not Allow.', [], 405);       
    }

    public function updateMemberRole(Request $request)
    {
        if($request->isPut()){
            $data = $request->getJson();
            $requestModel = new UpdateMemberRole();
            $requestModel->loadData($data);
            if($requestModel->validate()){
                $result = $this->roleService->updateMemberRole($data['Account'], $data['Role']);
                return $result=='success' ? $this->sendResponse($result, '修改成功') : $this->sendError($result, '修改失敗');
            }
        }  
        return $this->sendError('Method Not Allow.', [], 405);       
    }

    public function destroy(Request $request)
    {
        if($request->isDelete()){
            $id = $request->getBody()['id'] ?? '';
            $result = $this->roleService->delete($id);       
            return $result=='success' ? $this->sendResponse($result, '刪除成功') : $this->sendError($result, '刪除失敗');
        }
        return $this->sendError('Method Not Allow.', [], 405);        
    }
}
