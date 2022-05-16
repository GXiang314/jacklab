<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\middlewares\hasRoleMiddleware;
use app\middlewares\isLoginMiddleware;
use app\requestModel\AddName;
use app\requestModel\UpdateName;
use app\services\AcademicService;

class AcademicController extends Controller{
    
    private $academicService;
    
    public function __construct()
    {
        $this->academicService = new AcademicService();
        $this->registerMiddleware(new isLoginMiddleware(['index', 'show', 'store', 'update', 'destroy']));
        $this->registerMiddleware(new hasRoleMiddleware(['index', 'show', 'store', 'update', 'destroy']));
    }

    public function index()
    {
        $data = $this->academicService->getAll();
        return isset($data) ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
    }
        
    public function show(Request $request)
    {
        if($request->isGet()){
            $id = $request->getBody()['id'] ?? '';
            $data = $this->academicService->getClass($id);
            return isset($data) ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function store(Request $request)
    {
        if($request->isPost()){
            $requestModel = new AddName();
            $requestModel->loadData($request->getJson());
            if($requestModel->validate()){
                $result = $this->academicService->add($requestModel->Name);
            }
            return $result == 'success'? $this->sendResponse($result, 'success') : $this->sendError($result);
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }
    
    public function update(Request $request)
    {
        if($request->isPut()){
            $requestModel = new UpdateName();
            $requestModel->loadData($request->getJson());
            if($requestModel->validate()){
                $result = $this->academicService->update($requestModel->Id,$requestModel->Name);
            }
            return $result == 'success'? $this->sendResponse($result, 'success') : $this->sendError($result);
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }
    
    public function destroy(Request $request)
    {
        if($request->isDelete()){
            $id = $request->getBody()['id'] ?? '';
            $result = $this->academicService->delete($id);
            return $result == 'success'? $this->sendResponse($result, 'success') : $this->sendError($result);
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }
}