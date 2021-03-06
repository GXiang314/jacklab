<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Exception\MethodNotAllowException;
use app\core\Request;
use app\middlewares\hasRoleMiddleware;
use app\middlewares\isLoginMiddleware;
use app\requestModel\AddClassName;
use app\requestModel\UpdateName;
use app\services\ClassService;

class ClassesController extends Controller
{

    private $classService;

    public function __construct()
    {
        $this->classService = new ClassService();
        $this->registerMiddleware(new isLoginMiddleware(['index', 'show', 'store', 'update', 'destroy']));
        $this->registerMiddleware(new hasRoleMiddleware(['index', 'show', 'store', 'update', 'destroy']));
    }

    public function index()
    {
        $data = $this->classService->getAll();
        return isset($data) ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
    }

    public function show(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->getBody()['id'] ?? '';
            $data = $this->classService->getStudent($id);
            return isset($data) ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
        }
        throw new MethodNotAllowException();
    }

    public function store(Request $request)
    {
        if ($request->isPost()) {
            $requestModel = new AddClassName();
            $requestModel->loadData($request->getbody());
            if ($requestModel->validate()) {
                $result = $this->classService->add($requestModel->Name, $requestModel->Academic_Id);
                return $result == 'success' ? $this->sendResponse($result, '新增成功') : $this->sendError($result);
            } else {
                return $this->sendError($requestModel->getFirstError());
            }
        }
        throw new MethodNotAllowException();
    }

    public function update(Request $request)
    {
        if ($request->isPut()) {
            $requestModel = new UpdateName();
            $requestModel->loadData($request->getbody());
            if ($requestModel->validate()) {
                $result = $this->classService->update($requestModel->Id, $requestModel->Name);
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
            $result = $this->classService->delete($id);
            return $result == 'success' ? $this->sendResponse($result, '刪除成功') : $this->sendError($result);
        }
        throw new MethodNotAllowException();
    }
}
