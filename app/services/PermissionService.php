<?php

namespace app\services;

use app\core\DbModel;
use app\model\member_role;
use app\model\permission;

class PermissionService{
    public function __construct()
    {
        $this->install();
    }

    public function getAll()
    {
        $data = permission::get('permission');
        return $data;
    }

    private function install()
    {

        $permission_Array = [        
            '新增競賽記錄' =>'GameRecordController@store',
            '編輯競賽記錄' =>'GameRecordController@update',
            '刪除競賽記錄' =>'GameRecordController@destroy',
        
            '建立專案' =>'ProjectRecordController@store',
            '編輯專案內容' =>'ProjectRecordController@update',
            '刪除專案' =>'ProjectRecordController@destroy',
            
            '新增專案記錄' =>'ProjectRecordController@storeRecord',
            '編輯專案記錄' =>'ProjectRecordController@updateRecord',
            '刪除專案記錄' =>'ProjectRecordController@destroyRecord',

            '取得會議列表' =>'MeetController@index',
            '取得該會議記錄' =>'MeetController@show',
            '新增會議記錄' =>'MeetController@store',
            '編輯會議記錄' =>'MeetController@update',
            '刪除會議記錄' =>'MeetController@destroy',
        ];
        $dbCount = permission::count('permission');
        $arrCount = count($permission_Array);
        $index = 1;
        if($dbCount == 0){
            foreach($permission_Array as $pKey => $pValue){
                permission::create('permission', [
                    'Id' =>$index,
                    'Name' =>$pKey,
                    'Url' =>$pValue
                ]);
                $index += 1;
            }
        }else if($dbCount < $arrCount){
            foreach($permission_Array as $pKey => $pValue){
                if($index <= $dbCount) {
                    $index += 1;
                    continue;
                }
                permission::create('permission', [
                    'Id' => $index,
                    'Name' =>$pKey,
                    'Url' =>$pValue
                ]);
                $index += 1;
            }
        }
    }
}