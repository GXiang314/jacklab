<?php

namespace app\model;

use app\core\DbModel;

class proj_tag extends DbModel{

    public int $Id;
    public string $Name;    
    public int $Project_Id;


    public static function table(): string
    {
        return 'proj_tag';
    }

    public function attributes(): array
    {
        return ['Name', 'Project_Id'];
    }

    public function save()
    {        
        return parent::save();
    }

    public function rules(): array
    {            
        return [
            'Name' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>20],                
            ],
            'Project_Id' => [
                self::RULE_REQUIRED,
            ]
        ];
    }

}