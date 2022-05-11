<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\middlewares\isLoginMiddleware;
use app\requestModel\AddGame_record;
use app\requestModel\UpdateGame_record;
use app\services\GameRecordService;

class GameRecordController extends Controller
{

    private $gameRecordService;
    public function __construct()
    {
        $this->gameRecordService = new GameRecordService();
        $this->registerMiddleware(new isLoginMiddleware(['index', 'store', 'show', 'update', 'destroy']));
    }

    public function index()
    {
        $data = $this->gameRecordService->getAll();
        return (isset($data)) ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
    }

    public function show(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->getBody()['id'] ?? '';
            $data = $this->gameRecordService->getOne($id);
            return (!empty($data)) ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function store(Request $request)
    {
        if ($request->isPost()) {
            $data =  $request->getBody() ?? '';
            $requestModel = new AddGame_record();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $res = $this->gameRecordService->add($data, $requestModel->Files ?? null);
                return ($res == 'success') ? $this->sendResponse($res, 'success') : $this->sendError($res ?? '新增失敗');
            }else{
                return $this->sendError($requestModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function update(Request $request)
    {
        if ($request->isPut()) {
            $data =  $request->getBody() ?? '';
            $requestModel = new UpdateGame_record();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $res = $this->gameRecordService->update(
                    $requestModel->Id, 
                    $data, 
                    $requestModel->Files ?? null, 
                    $requestModel->IsClearOld ?? [], 
                );
                return ($res == 'success') ? $this->sendResponse($requestModel, 'success') : $this->sendError($res ?? '新增失敗');
            }else{
                return $this->sendError($requestModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function destroy(Request $request)
    {
        if($request->isDelete()){
            $id = $request->getBody()['id'] ?? '';
            $result = $this->gameRecordService->delete($id);
            return $result == 'success'? $this->sendResponse($result, 'success') : $this->sendError($result);
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

}
