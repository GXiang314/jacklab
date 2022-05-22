<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\middlewares\hasRoleMiddleware;
use app\middlewares\isLoginMiddleware;
use app\requestModel\AddBook;
use app\requestModel\UpdateBook;
use app\requestModel\UpdateImage;
use app\services\BookService;

class BookController extends Controller
{

    private $bookService;

    public function __construct()
    {
        $this->bookService = new BookService();
        $this->registerMiddleware(new isLoginMiddleware(['store', 'update', 'destroy']));
        $this->registerMiddleware(new hasRoleMiddleware(['store', 'update', 'destroy']));
    }

    public function index(Request $request)
    {
        if ($request->isGet()) {
            $page = $request->getBody()['page'] ?? 1;
            $page = (!is_numeric($page)) ? 1 : intval($page);
            $search = $request->getBody()['search'] ?? '';
            $search = (empty(trim($search))) ? null : $search;
            $data = $this->bookService->getAll($page, $search);
            return $data ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
        }
        return $this->sendError('Method Not Allow.', [], 405);
       
    }

    public function show(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->getBody()['id'] ?? '';
            $data = $this->bookService->getOne($id);
            return $data ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function store(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->getBody();
            $requestModel = new AddBook();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->bookService->add($data, $requestModel->Authors, $requestModel->Image);
                return $result == 'success' ? $this->sendResponse($result, '新增成功') : $this->sendError('新增失敗', $result);
            } else {
                return $this->sendError('欄位格式錯誤', $requestModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function update(Request $request)
    {
        if ($request->isPut()) {
            $data = $request->getJson();
            $requestModel = new UpdateBook();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->bookService->update($requestModel->Id, $data, $requestModel->Authors);
                return $result == 'success' ? $this->sendResponse($result, '修改成功') : $this->sendError('修改失敗', $result);
            } else {
                return $this->sendError('欄位格式錯誤', $requestModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function updateImage(Request $request)
    {
        if ($request->isPut()) {
            $data = $request->getBody();
            $requestModel = new UpdateImage();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->bookService->updateImage($requestModel->Id, $requestModel->Image);
                return $result == 'success' ? $this->sendResponse($result, '修改成功') : $this->sendError('修改失敗', $result);
            } else {
                return $this->sendError('欄位格式錯誤', $requestModel->errors);
            }
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }

    public function destroy(Request $request)
    {
        if ($request->isDelete()) {
            $id = $request->getBody()['id'] ?? '';
            $result = $this->bookService->delete($id);
            return $result == 'success' ? $this->sendResponse($result, '刪除成功') : $this->sendError('刪除失敗', $result);
        }
        return $this->sendError('Method Not Allow.', [], 405);
    }
}
