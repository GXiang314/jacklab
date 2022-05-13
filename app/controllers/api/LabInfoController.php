<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\middlewares\hasRoleMiddleware;
use app\middlewares\isLoginMiddleware;
use app\model\lab_info;
use app\requestModel\UpdateLabinfo;
use app\services\labinfoService;

class LabInfoController extends Controller
{

    private $labinfoService;

    public function __construct()
    {
        $this->labinfoService = new labinfoService();
        $this->registerMiddleware(new isLoginMiddleware(['index', 'show', 'store', 'update', 'destroy']));
        $this->registerMiddleware(new hasRoleMiddleware(['index', 'show', 'store', 'update', 'destroy']));
    }

    public function index()
    {
        $data = $this->labinfoService->getAll();
        return isset($data) ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
    }

    public function show(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->getBody()['id'] ?? '';
            $data = $this->labinfoService->getOne($id);
            return isset($data) ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function store(Request $request)
    {
        if ($request->isPost()) {
            $requestModel = new lab_info();
            $requestModel->loadData($request->getJson());
            if ($requestModel->validate()) {
                $result = $this->labinfoService->add($requestModel->Title, $requestModel->Content);
            }
            return $result == 'success' ? $this->sendResponse($result, 'success') : $this->sendError($result);
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function update(Request $request)
    {
        if ($request->isPut()) {
            $requestModel = new UpdateLabinfo();
            $requestModel->loadData($request->getJson());
            if ($requestModel->validate()) {
                $result = $this->labinfoService->update($requestModel->Id, $requestModel->Title, $requestModel->Content);
            }
            return $result == 'success' ? $this->sendResponse($result, 'success') : $this->sendError($result);
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function destroy(Request $request)
    {
        if ($request->isDelete()) {
            $id = $request->getBody()['id'] ?? '';
            $result = $this->labinfoService->delete($id);
            return $result == 'success' ? $this->sendResponse($result, 'success') : $this->sendError($result);
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }
}
