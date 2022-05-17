<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\services\GameRecordService;
use app\services\MeetService;
use app\services\ProjectRecordService;

class DownloadController extends Controller{
    
    private $meetService;
    private $gameRecordService;
    private $projectRecordService;
    
    public function __construct()
    {
        $this->meetService = new MeetService();
        $this->gameRecordService = new GameRecordService();
        $this->projectRecordService = new ProjectRecordService();
    }

    public function download_Meet(Request $request)
    {
        if($request->isGet()){
            $id = $request->getBody()['id'] ?? '';
            $file = $this->meetService->getFile($id);    
            $root = dirname(dirname(dirname(__DIR__))). "\public";   
            if(!empty($file)){
                if (file_exists($root.$file['Url'])) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.basename($file['Name']).'"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($root.$file['Url']));
                    readfile($root.$file['Url']);
                    exit;
                }
            }            
            return $this->sendError('找不到資源，請聯繫網站管理員');
        }        
    }

    public function download_Game(Request $request)
    {
        if($request->isGet()){
            $id = $request->getBody()['id'] ?? '';
            $file = $this->gameRecordService->getFile($id);
            $root = dirname(dirname(dirname(__DIR__))). "\public";   
            if(!empty($file)){
                if (file_exists($root.$file['Url'])) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.basename($file['Name']).'"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($root.$file['Url']));
                    readfile($root.$file['Url']);
                    exit;
                }
            }            
            return $this->sendError('找不到資源，請聯繫網站管理員');
        }        
    }


    public function download_Project(Request $request)
    {
        if($request->isGet()){
            $id = $request->getBody()['id'] ?? '';
            $file = $this->projectRecordService->getFile($id);
            $root = dirname(dirname(dirname(__DIR__))). "\public";   
            if(!empty($file)){
                if (file_exists($root.$file['Url'])) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.basename($file['Name']).'"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($root.$file['Url']));
                    readfile($root.$file['Url']);
                    exit;
                    return $this->sendResponse('success', '下載成功');
                }
            }            
            return $this->sendError('找不到資源，請聯繫網站管理員');
        }        
    }  
    
}