<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\middlewares\hasRoleMiddleware;
use app\middlewares\isLoginMiddleware;
use app\requestModel\AddMeeting;
use app\requestModel\UpdateMeeting;
use app\requestModel\UpdateName;
use app\services\MeetService;

class MeetController extends Controller
{

    private $meetService;
    public function __construct()
    {
        $this->meetService = new MeetService();
        $this->registerMiddleware(new isLoginMiddleware(['index', 'store', 'show', 'update', 'destroy']));
        $this->registerMiddleware(new hasRoleMiddleware(['index', 'store', 'show', 'update', 'destroy']));
    }

    public function index(Request $request)
    {
        if ($request->isGet()) {
            $page = $request->getBody()['page'] ?? 1;
            $page = (!is_numeric($page)) ? 1 : intval($page);
            $search = $request->getBody()['search'] ?? '';
            $search = (empty(trim($search))) ? null : $search;
            $data = $this->meetService->getAll($page, $search);
            return (isset($data)) ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function show(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->getBody()['id'] ?? '';
            $data = $this->meetService->getOne($id);
            return (!empty($data)) ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function store(Request $request)
    {
        if ($request->isPost()) {
            $data =  $request->getBody() ?? '';
            $requestModel = new AddMeeting();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $res = $this->meetService->add($data, $requestModel->Files ?? null, $requestModel->Tag ?? null);
                return ($res == 'success') ? $this->sendResponse($res, '新增成功') : $this->sendError('新增失敗', $res);
            } else {
                return $this->sendError($requestModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function update(Request $request)
    {
        if ($request->isPut()) {
            $data =  $request->getBody() ?? '';
            $requestModel = new UpdateMeeting();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $res = $this->meetService->update(
                    $requestModel->Id,
                    $data,
                    $requestModel->Files ?? null,
                    $requestModel->Tag ?? null,
                    $requestModel->IsClearOld ?? [],
                );
                return ($res == 'success') ? $this->sendResponse($res, '修改成功') : $this->sendError('修改失敗', $res);
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
            $result = $this->meetService->delete($id);
            return $result == 'success' ? $this->sendResponse($result, '刪除成功') : $this->sendError('刪除失敗', $result);
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }
}
