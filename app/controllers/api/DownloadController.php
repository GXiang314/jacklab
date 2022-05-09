<?php

namespace app\controllers\api;

use app\core\Controller;
use app\core\Request;
use app\services\MeetService;

class DownloadController extends Controller{
    
    private $meetService;
    
    public function __construct()
    {
        $this->meetService = new MeetService();
    }

    public function download_Meet(Request $request)
    {
        if($request->isGet()){
            $fileName = $request->getBody()['file'] ?? '';
            $id = $request->getBody()['id'] ?? '';
            $file = $this->meetService->getFile($fileName, $id);
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
    }

    public function download_Game($fileName)
    {
        $zip = $this->gameRecordService->game_FileZipGenerator($id);
        try{
            if($zip !='error'){
                $path = storage_path('app\\game\\').$zip;
                header('Content-disposition: attachment; filename='.$zip);
                header('Content-type: application/zip');
                readfile($path);
            }
        }catch(Exception){
            return $this->sendError('找不到資源，請聯繫網站管理員');
        }
        finally{
            Storage::delete('game\\'.$zip);
        }
        return ($zip != 'error')? $this->sendResponse($zip,'success') : $this->sendError('找不到資源，請聯繫網站管理員');
    }
    */
}