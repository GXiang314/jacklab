<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\services\PermissionService;

class PermissionController extends Controller
{

    //
    private $permissionService;
    public function __construct()
    {
        $this->permissionService = new PermissionService();
    }
    public function index()
    {
        $data = $this->permissionService->getAll();
        return ($data != []) ? $this->sendResponse($data, '所有權限') : $this->sendError('沒有資料');
    }
    public function show(Request $request)
    {
    }

    public function addrole_Permission()
    {
    }
}
