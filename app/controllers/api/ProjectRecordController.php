<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\middlewares\isLoginMiddleware;
use app\requestModel\AddGame_record;
use app\requestModel\AddProject;
use app\requestModel\AddProject_record;
use app\requestModel\UpdateGame_record;
use app\requestModel\UpdateProject;
use app\requestModel\UpdateProject_record;
use app\services\ProjectRecordService;
use app\core\Exception\MethodNotAllowException;
use app\middlewares\hasRoleMiddleware;

class ProjectRecordController extends Controller
{

    private $projectRecordService;
    public function __construct()
    {
        $this->projectRecordService = new ProjectRecordService();
        $this->registerMiddleware(new isLoginMiddleware([
            'index', 'show','store', 'storeRecord', 'update', 'updateRecord', 'destroy', 'destroyRecord'
        ]));
        $this->registerMiddleware(new hasRoleMiddleware([
            'store', 'update', 'destroy', 'destroyRecord'
        ]));
    }

    public function index(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->getBody()['id'] ?? '';
            $page = $request->getBody()['page'] ?? 1;
            $page = (!is_numeric($page)) ? 1 : intval($page);
            $search = $request->getBody()['search'] ?? '';
            $search = (empty(trim($search))) ? null : $search;
            $data = $this->projectRecordService->getAll($id, $page, $search);
            return $data ? $this->sendResponse($data, '專案內容') : $this->sendResponse('', '沒有資料');
        }
        throw new MethodNotAllowException();
    }

    public function getTag(Request $request)
    {
        if ($request->isGet()) {            
            $search = $request->getBody()['search'] ?? '';
            $search = (empty(trim($search))) ? null : $search;
            $data = $this->projectRecordService->getProjectTag($search);
            return $data ? $this->sendResponse($data, '標籤列表') : $this->sendResponse('', '沒有資料');
        }
        throw new MethodNotAllowException();
    }

    public function show(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->getBody()['id'] ?? '';
            $page = $request->getBody()['page'] ?? 1;
            $page = (!is_numeric($page)) ? 1 : intval($page);
            $search = $request->getBody()['search'] ?? '';
            $search = (empty(trim($search))) ? null : $search;
            $data = $this->projectRecordService->getOne($id, $page, $search);
            return (!empty($data)) ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
        }
        throw new MethodNotAllowException();
    }

    public function store(Request $request)
    {
        if ($request->isPost()) {
            $data =  $request->getbody() ?? '';
            $requestModel = new AddProject();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                if(!in_array($data['USER'], $requestModel->Member)) return $this->sendError("不要排擠自己");
                $res = $this->projectRecordService->create($data, $requestModel->Tag ?? null);
                return ($res == 'success') ? $this->sendResponse($res, '建立成功') : $this->sendError($res);
            } else {
                return $this->sendError($requestModel->getFirstError());
            }
        }
        throw new MethodNotAllowException();
    }

    public function storeRecord(Request $request)
    {
        if ($request->isPost()) {
            $data =  $request->getBody() ?? '';
            $requestModel = new AddProject_record();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $res = $this->projectRecordService->addRecord($data, $requestModel->File);
                return ($res == 'success') ? $this->sendResponse($res, '新增成功') : $this->sendError($res);
            } else {
                return $this->sendError($requestModel->getFirstError());
            }
        }
        throw new MethodNotAllowException();
    }

    public function update(Request $request)
    {
        if ($request->isPut()) {
            $data =  $request->getbody() ?? '';
            $requestModel = new UpdateProject();
            $requestModel->loadData($data);

            if ($requestModel->validate()) {
                if(!in_array($data['USER'], $requestModel->Member)) return $this->sendError("不要排擠自己");
                $res = $this->projectRecordService->update(
                    $requestModel->Id,
                    $data,
                    $requestModel->Tag ?? null
                );
                return ($res == 'success') ? $this->sendResponse($res, '修改成功') : $this->sendError( $res);
            } else {
                return $this->sendError($requestModel->getFirstError());
            }
        }
        throw new MethodNotAllowException();
    }

    public function updateRecord(Request $request)
    {
        if ($request->isPut()) {
            $data =  $request->getBody() ?? '';
            $requestModel = new UpdateProject_record();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $res = $this->projectRecordService->updateRecord(
                    $requestModel->Id,
                    $data,
                    $requestModel->File
                );
                return ($res == 'success') ? $this->sendResponse($res, '修改成功') : $this->sendError($res);
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
            $result = $this->projectRecordService->delete($id);
            return $result == 'success' ? $this->sendResponse($result, '刪除成功') : $this->sendError($result);
        }
        throw new MethodNotAllowException();
    }

    public function destroyRecord(Request $request)
    {
        if ($request->isDelete()) {
            $id = $request->getBody()['id'] ?? '';
            $result = $this->projectRecordService->deleteRecord($id);
            return $result == 'success' ? $this->sendResponse($result, '刪除成功') : $this->sendError($result);
        }
        throw new MethodNotAllowException();
    }
}
