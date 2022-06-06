<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Exception\MethodNotAllowException;
use app\core\Request;
use app\requestModel\AddName;
use app\requestModel\UpdateName;
use app\services\GameManagerService;

class GameManagerController extends Controller
{

    private $gameManagerService;
    public function __construct()
    {
        $this->gameManagerService = new GameManagerService();
    }

    public function index()
    {
        $data = $this->gameManagerService->getAll();
        return ($data != []) ? $this->sendResponse($data, '所有競賽類型') : $this->sendError('沒有資料');
    }

    public function store(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->getbody();
            $requestModel = new AddName();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $res = $this->gameManagerService->add($data['Name']);
                return ($res == 'success') ? $this->sendResponse($res, '建立成功') : $this->sendError($res);
            } else {
                return $this->sendError($requestModel->getFirstError());
            }
        }
        throw new MethodNotAllowException();
    }

    public function show(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->getBody()['id'] ?? '';
            $data = $this->gameManagerService->getRecord($id);
            return !empty($data) ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
        }
        throw new MethodNotAllowException();
    }

    public function update(Request $request)
    {
        if ($request->isPut()) {
            $data = $request->getbody();
            $requestModel = new UpdateName();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $res = $this->gameManagerService->update($data['Id'], $data['Name']);
                return ($res == 'success') ? $this->sendResponse($res, '修改成功') : $this->sendError( $res);
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
            $result = $this->gameManagerService->delete($id);
            return $result == 'success' ? $this->sendResponse($result, '刪除成功') : $this->sendError( $result);
        }
        throw new MethodNotAllowException();
    }
}
