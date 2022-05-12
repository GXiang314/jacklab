<?php

namespace app\model;

use app\core\DbModel;

class proj_member extends DbModel{

    public int $Id;
    public int $Project_Id;
    public string $Account;    


    public function table(): string
    {
        return 'proj_member';
    }

    public function attributes(): array
    {
        return ['Project_Id', 'Account'];
    }

    public function save()
    {        
        return parent::save();
    }

    public function rules(): array
    {            
        return [
            'Project_Id' => [
                self::RULE_REQUIRED,
            ],
            'Account' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>50],                
            ],
            
        ];
    }

}