<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\services\GameRecordService;
use app\services\MeetService;

class DownloadController extends Controller{
    
    private $meetService;
    private $gameRecordService;
    
    public function __construct()
    {
        $this->meetService = new MeetService();
        $this->gameRecordService = new GameRecordService();
    }

    public function download_Meet(Request $request)
    {
        if($request->isGet()){
            $id = $request->getBody()['id'] ?? '';
            $file = $this->meetService->getFile($id);       
            if(!empty($file)){
                if (file_exists($file['Url'])) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.basename($file['Name']).'"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file['Url']));
                    readfile($file['Url']);
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
            if(!empty($file)){
                if (file_exists($file['Url'])) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.basename($file['Name']).'"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file['Url']));
                    readfile($file['Url']);
                    exit;
                }
            }            
            return $this->sendError('找不到資源，請聯繫網站管理員');
        }        
    }

/*
    public function download_Project($fileName)
    {
        $file = $this->projectService->getFile($fileName);
        if($file !=[]){
            return Storage::download($file['Url'],$file['Name']);
        }
        return $this->sendError('找不到資源，請聯繫網站管理員');
    }*/    
    
}