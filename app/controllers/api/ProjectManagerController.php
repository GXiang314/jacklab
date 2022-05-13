<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\middlewares\hasRoleMiddleware;
use app\middlewares\isLoginMiddleware;
use app\requestModel\AddName;
use app\requestModel\UpdateName;
use app\services\ProjectManagerService;

class ProjectManagerController extends Controller
{

    private $projectManagerService;
    public function __construct()
    {
        $this->projectManagerService = new ProjectManagerService();
        $this->registerMiddleware(new isLoginMiddleware(['index', 'show', 'store', 'update', 'destroy']));
        $this->registerMiddleware(new hasRoleMiddleware(['store', 'update', 'destroy']));

    }

    public function index()
    {
        $data = $this->projectManagerService->getAll();
        return ($data !=[])? $this->sendResponse($data,'所有專案性質'):$this->sendError('沒有資料');
    }

    public function store(Request $request)
    {
        if($request->isPost()){
            $data = $request->getJson();
            $requestModel = new AddName();
            $requestModel->loadData($data);
            if($requestModel->validate()){
                $res = $this->projectManagerService->add($data['Name']);
                return ($res =='success')? $this->sendResponse($res,'success') : $this->sendError('error');
            }else{
                return $this->sendError($requestModel->errors);
            }
        }       
        return $this->sendError('Method Not Allow', [], 405);
    }

    public function show(Request $request)
    {
        if($request->isGet()){
            $id = $request->getBody()['id'] ?? '%';
            $page = $request->getBody()['page'] ?? 1;
            $search = $request->getBody()['search'] ?? null;
            $data = $this->projectManagerService->getProject($id, $page, $search);
            return !empty($data) ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function update(Request $request)
    {
        if($request->isPut()){
            $data = $request->getJson();
            $requestModel = new UpdateName();
            $requestModel->loadData($data);
            if($requestModel->validate()){
                $res = $this->projectManagerService->update($data['Id'],$data['Name']);
                return ($res =='success')? $this->sendResponse($res,'success') : $this->sendError('error');
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
            $result = $this->projectManagerService->delete($id);
            return $result == 'success'? $this->sendResponse($result, 'success') : $this->sendError($result);
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

}
