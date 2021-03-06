<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Exception\MethodNotAllowException;
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
        throw new MethodNotAllowException();
       
    }

    public function show(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->getBody()['id'] ?? '';
            $data = $this->bookService->getOne($id);
            return $data ? $this->sendResponse($data, 'success') : $this->sendResponse('', '沒有資料');
        }
        throw new MethodNotAllowException();
    }

    public function store(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->getBody();
            $requestModel = new AddBook();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->bookService->add($data, $requestModel->Authors, $requestModel->Image);
                return $result == 'success' ? $this->sendResponse($result, '新增成功') : $this->sendError($result);
            } else {
                return $this->sendError($requestModel->getFirstError());
            }
        }
        throw new MethodNotAllowException();
    }

    public function update(Request $request)
    {
        if ($request->isPut()) {
            $data = $request->getbody();
            $requestModel = new UpdateBook();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->bookService->update($requestModel->Id, $data, $requestModel->Authors);
                return $result == 'success' ? $this->sendResponse($result, '修改成功') : $this->sendError($result);
            } else {
                return $this->sendError($requestModel->getFirstError());
            }
        }
        throw new MethodNotAllowException();
    }

    public function updateImage(Request $request)
    {
        if ($request->isPut()) {
            $data = $request->getBody();
            $requestModel = new UpdateImage();
            $requestModel->loadData($data);
            if ($requestModel->validate()) {
                $result = $this->bookService->updateImage($requestModel->Id, $requestModel->Image);
                return $result == 'success' ? $this->sendResponse($result, '修改成功') : $this->sendError($result);
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
            $result = $this->bookService->delete($id);
            return $result == 'success' ? $this->sendResponse($result, '刪除成功') : $this->sendError($result);
        }
        throw new MethodNotAllowException();
    }
}
