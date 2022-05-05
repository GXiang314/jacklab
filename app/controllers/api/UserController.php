<?php
namespace app\controllers\api;

use app\controllers\BaseController;
use app\services\MemberService;

class UserController extends BaseController{

    private $memberService;
    public function __construct()
    {
        $this->memberService = new MemberService();
        
    }
}