<?php

namespace app\services;

use app\core\DbModel;
use app\model\member_role;
use app\model\permission;
use app\model\permission_group;

class PermissionService
{
    public function __construct()
    {
        $this->install();
    }

    public function getAll()
    {
        $statement = DbModel::prepare("
        select 
            pg.*
        from 
            permission_group as pg;
        ");
        $statement->execute();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $statement = null;
        if(!empty($data)){
            $index = 0;
            foreach ($data as $row) {
                $data[$index++]['list'] = permission::get('permission', [
                    'Permission_group' => $row['Id']
                ]);
            }
        }
        
        return $data;
    }

    private function install()
    {
        $permission_Group = [
            '專案管理' => 'P001',
            '刪除專案' => 'P002',
            '會議記錄管理' => 'M001',
            '刪除會議記錄' => 'M002',
        ];

        $permission_Array = [
            // '新增競賽記錄' =>'GameRecordController@store',
            // '編輯競賽記錄' =>'GameRecordController@update',
            // '刪除競賽記錄' =>'GameRecordController@destroy',

            '建立專案' => ['ProjectRecordController@store', 'P001'],
            '編輯專案' => ['ProjectRecordController@update', 'P001'],
            '刪除專案記錄' => ['ProjectRecordController@destroyRecord', 'P001'],

            '刪除專案' => ['ProjectRecordController@destroy', 'P002'],

            '新增會議記錄' => ['MeetController@store', 'M001'],
            '編輯會議記錄' => ['MeetController@update', 'M001'],
            '刪除會議記錄' => ['MeetController@destroy', 'M002'],
        ];

        $dbCount = permission_group::count('permission_group');
        $arrCount = count($permission_Group);
        $index = 1;
        if ($dbCount == 0) {
            foreach ($permission_Group as $pKey => $pValue) {
                permission_group::create('permission_group', [
                    'Id' => $pValue,
                    'Name' => $pKey,
                ]);
                $index += 1;
            }
        } else if ($dbCount < $arrCount) {
            foreach ($permission_Group as $pKey => $pValue) {
                if ($index <= $dbCount) {
                    $index += 1;
                    continue;
                }
                permission_group::create('permission_group', [
                    'Id' => $pValue,
                    'Name' => $pKey,
                ]);
                $index += 1;
            }
        }

        $dbCount = permission::count('permission');
        $arrCount = count($permission_Array);
        $index = 1;
        if ($dbCount == 0) {
            foreach ($permission_Array as $pKey => $pValue) {
                permission::create('permission', [
                    'Id' => $index,
                    'Name' => $pKey,
                    'Url' => $pValue[0],
                    'Permission_group' => $pValue[1],
                ]);
                $index += 1;
            }
        } else if ($dbCount < $arrCount) {
            foreach ($permission_Array as $pKey => $pValue) {
                if ($index <= $dbCount) {
                    $index += 1;
                    continue;
                }
                permission::create('permission', [
                    'Id' => $index,
                    'Name' => $pKey,
                    'Url' => $pValue[0],
                    'Permission_group' => $pValue[1],
                ]);
                $index += 1;
            }
        }
    }
}
