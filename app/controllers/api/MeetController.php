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
use app\core\Exception\MethodNotAllowException;

class MeetController extends Controller
{

    private $meetService;
    public function __construct()
    {
        $this->meetService = new MeetService();
        $this->registerMiddleware(new isLoginMiddleware(['index', 'show', 'store', 'update', 'destroy']));
        $this->registerMiddleware(new hasRoleMiddleware(['store', 'update', 'destroy']));
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
        throw new MethodNotAllowException();
    }

    public function getTag(Request $request)
    {
        if ($request->isGet()) {            
            $search = $request->getBody()['search'] ?? '';
            $search = (empty(trim($search))) ? null : $search;
            $data = $this->meetService->getMeetingTag($search);
            return $data ? $this->sendResponse($data, '標籤列表') : $this->sendResponse('', '沒有資料');
        }
        throw new MethodNotAllowException();
    }

    public function show(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->getBody()['id'] ?? '';
            $data = $this->meetService->getOne($id);
            return (!empty($data)) ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
        }
        throw new MethodNotAllowException();
    }

    public function store(Request $request)
    {
        if ($request->isPost()) {
            $data =  $request->getBody() ?? '';
            $requestModel = new AddMeeting();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                if(!in_array($request['USER'], $requestModel->Member)) return $this->sendError("不要排擠自己");
                $res = $this->meetService->add($data, $requestModel->Files ?? null, $requestModel->Tag ?? null);
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
            $data =  $request->getBody() ?? '';
            $requestModel = new UpdateMeeting();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                if(!in_array($request['USER'], $requestModel->Member)) return $this->sendError("不要排擠自己");
                $res = $this->meetService->update(
                    $requestModel->Id,
                    $data,
                    $requestModel->Files ?? null,
                    $requestModel->Tag ?? null,
                    $requestModel->IsClearOld ?? [],
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
            $result = $this->meetService->delete($id);
            return $result == 'success' ? $this->sendResponse($result, '刪除成功') : $this->sendError($result);
        }
        throw new MethodNotAllowException();
    }
}
