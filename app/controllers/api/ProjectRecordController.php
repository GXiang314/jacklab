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

class ProjectRecordController extends Controller
{

    private $projectRecordService;
    public function __construct()
    {
        $this->projectRecordService = new ProjectRecordService();
        $this->registerMiddleware(new isLoginMiddleware([
            'index', 'store', 'storeRecord', 'update', 'updateRecord', 'destroy', 'destroyRecord']));
    }

    public function index(Request $request)
    {       
        if ($request->isGet()) {
            $id = $request->getBody()['id'] ?? '';
            $page = $request->getBody()['page'] ?? 1;
            $search = $request->getBody()['search'] ?? null;
            $data = $this->projectRecordService->getAll($id, $page, $search);
            return $data ? $this->sendResponse($data, '專案內容') : $this->sendResponse('', '沒有資料');
        }
        return $this->sendError('Method Not Allow', [], 405);
    }

    // public function show(Request $request)
    // {
    //     if ($request->isGet()) {
    //         $id = $request->getBody()['id'] ?? '';
    //         $data = $this->projectRecordService->get($id);
    //         return (!empty($data)) ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
    //     }
    //     return $this->sendError('Method Not Allow.', [], 405);
    // }

    public function store(Request $request)
    {
        if ($request->isPost()) {
            $data =  $request->getJson() ?? '';
            $requestModel = new AddProject();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $res = $this->projectRecordService->create($data, $requestModel->Tag ?? null);
                return ($res == 'success') ? $this->sendResponse($res, 'success') : $this->sendError($res ?? '新增失敗');
            } else {
                return $this->sendError($requestModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function storeRecord(Request $request)
    {
        if ($request->isPost()) {
            $data =  $request->getBody() ?? '';
            $requestModel = new AddProject_record();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $res = $this->projectRecordService->addRecord($data, $requestModel->File);
                return ($res == 'success') ? $this->sendResponse($res, 'success') : $this->sendError($res ?? '新增失敗');
            } else {
                return $this->sendError($requestModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function update(Request $request)
    {
        if ($request->isPut()) {
            $data =  $request->getJson() ?? '';
            $requestModel = new UpdateProject();
            $requestModel->loadData($data);

            if ($requestModel->validate()) {
                $res = $this->projectRecordService->update(
                    $requestModel->Id,
                    $data,
                    $requestModel->Tag ?? null                   
                );
                return ($res == 'success') ? $this->sendResponse($res, 'success') : $this->sendError($res ?? '修改失敗');
            } else {
                return $this->sendError($requestModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
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
                return ($res == 'success') ? $this->sendResponse($res, 'success') : $this->sendError($res ?? '修改失敗');
            } else {
                return $this->sendError($requestModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function destroy(Request $request)
    {
        if ($request->isDelete()) {
            $id = $request->getBody()['id'] ?? '';
            $result = $this->projectRecordService->delete($id);
            return $result == 'success' ? $this->sendResponse($result, 'success') : $this->sendError($result);
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }
    
    public function destroyRecord(Request $request)
    {
        if ($request->isDelete()) {
            $id = $request->getBody()['id'] ?? '';
            $result = $this->projectRecordService->deleteRecord($id);
            return $result == 'success' ? $this->sendResponse($result, 'success') : $this->sendError($result);
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }
}
