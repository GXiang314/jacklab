<?php

namespace app\model;

use app\core\DbModel;

class proj_record extends DbModel{

    public int $Id;
    public string $Remark;
    public string $CreateTime;
    public string $Uploader;
    public int $Project_Id;
    public string $Deleted;




    public function table(): string
    {
        return 'proj_record';
    }

    public function attributes(): array
    {
        return ['Id', 'Remark', 'CreateTime', 'Uploader', 'Project_Id'];
    }

    public function save()
    {
        // $this->Id =         
        return parent::save();
    }

    public function rules(): array
    {            
        return [
            'Id' => [
                self::RULE_REQUIRED
            ],
            'Remark' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>500],                
            ],
            'CreateTime' => [
                self::RULE_REQUIRED,
            ],
            'Uploader' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>50],
            ],
            'Project_Id' => [self::RULE_REQUIRED],
            
            
        ];
    }

}