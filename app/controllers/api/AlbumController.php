<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\middlewares\hasRoleMiddleware;
use app\middlewares\isLoginMiddleware;
use app\requestModel\AddAlbum;
use app\requestModel\AddName;
use app\requestModel\UpdateAlbum;
use app\requestModel\UpdateName;
use app\services\AlbumService;

class AlbumController extends Controller
{

    private $albumService;

    public function __construct()
    {
        $this->albumService = new AlbumService();
        $this->registerMiddleware(new isLoginMiddleware(['store', 'update', 'destroy']));
        $this->registerMiddleware(new hasRoleMiddleware(['store', 'update', 'destroy']));
    }

    public function index()
    {
        $data = $this->albumService->getAll();
        return $data ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
    }

    public function show(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->getBody()['id'] ?? '';
            $data = $this->albumService->getOne($id);
            return $data ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function store(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->getBody();
            $requestModel = new AddAlbum();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->albumService->add($requestModel->Title, $requestModel->Image);
                return $result == 'success' ? $this->sendResponse($result, 'success') : $this->sendError($result);
            }else{
                return $this->sendError($requestModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function update(Request $request)
    {
        if ($request->isPut()) {
            $data = $request->getBody();
            $requestModel = new UpdateAlbum();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->albumService->update($requestModel->Id, $requestModel->Title, $requestModel->Image);
                return $result == 'success' ? $this->sendResponse($result, 'success') : $this->sendError($result);
            }else{
                return $this->sendError($requestModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function destroy(Request $request)
    {
        if ($request->isDelete()) {
            $id = $request->getBody()['id'] ?? '';
            $result = $this->albumService->delete($id);
            return $result == 'success' ? $this->sendResponse($result, 'success') : $this->sendError($result);
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }
}
