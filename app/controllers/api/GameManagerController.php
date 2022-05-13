<?php

namespace app\controllers\api;

use app\core\Controller;
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
        return ($data !=[])? $this->sendResponse($data,'所有競賽類型'):$this->sendError('沒有資料');
    }

    public function store(Request $request)
    {
        if($request->isPost()){
            $data = $request->getJson();
            $requestModel = new AddName();
            $requestModel->loadData($data);
            if($requestModel->validate()){
                $res = $this->gameManagerService->add($data['Name']);
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
            $id = $request->getBody()['id'] ?? '';
            $data = $this->gameManagerService->getRecord($id);
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
                $res = $this->gameManagerService->update($data['Id'],$data['Name']);
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
            $result = $this->gameManagerService->delete($id);
            return $result == 'success'? $this->sendResponse($result, 'success') : $this->sendError($result);
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

}
