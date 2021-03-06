<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\core\Exception\MethodNotAllowException;
use app\middlewares\hasRoleMiddleware;
use app\middlewares\isLoginMiddleware;
use app\model\lab_info;
use app\requestModel\UpdateLabinfo;
use app\services\LabinfoService;

class LabInfoController extends Controller
{

    private $labinfoService;

    public function __construct()
    {
        $this->labinfoService = new LabinfoService();
        $this->registerMiddleware(new isLoginMiddleware(['store', 'update', 'destroy']));
        $this->registerMiddleware(new hasRoleMiddleware(['store', 'update', 'destroy']));
    }

    public function index(Request $request)
    {        
        if ($request->isGet()) {
            $page = $request->getBody()['page'] ?? 1;
            $page = (!is_numeric($page)) ? 1 : intval($page);
            $search = $request->getBody()['search'] ?? '';
            $search = (empty(trim($search))) ? null : $search;
            $data = $this->labinfoService->getAll($page, $search);
            return $data ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
        }
        throw new MethodNotAllowException();
    }

    public function show(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->getBody()['id'] ?? '';
            $data = $this->labinfoService->getOne($id);
            return $data ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
        }
        throw new MethodNotAllowException();
    }

    public function store(Request $request)
    {
        if ($request->isPost()) {
            $requestModel = new lab_info();
            $requestModel->loadData($request->getbody());
            if ($requestModel->validate()) {
                $result = $this->labinfoService->add($requestModel->Title, $requestModel->Content);
                return $result == 'success' ? $this->sendResponse($result, '建立成功') : $this->sendError($result);
            } else {
                return $this->sendError($requestModel->getFirstError());
            }
        }
        throw new MethodNotAllowException();
    }

    public function update(Request $request)
    {
        if ($request->isPut()) {
            $requestModel = new UpdateLabinfo();
            $requestModel->loadData($request->getbody());
            if ($requestModel->validate()) {
                $result = $this->labinfoService->update($requestModel->Id, $requestModel->Title, $requestModel->Content);
                return $result == 'success' ? $this->sendResponse($result, '修改成功') : $this->sendError($result);
            } else {
                return $this->sendError($requestModel->getFirstError());
            }
        }
        throw new MethodNotAllowException();
    }

    public function destroy(Request $request)
    {
        if ($request->isDelete()) {
            $id = $request->getBody()['id'] ?? '';
            $result = $this->labinfoService->delete($id);
            return $result == 'success' ? $this->sendResponse($result, '刪除成功') : $this->sendError($result);
        }
        throw new MethodNotAllowException();
    }
}
