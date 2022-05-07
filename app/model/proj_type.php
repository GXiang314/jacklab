<?php

namespace app\model;

use app\core\DbModel;

class proj_type extends DbModel{

    public int $Id;
    public string $Name;    


    public function table(): string
    {
        return 'proj_type';
    }

    public function attributes(): array
    {
        return ['Name'];
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
            ]
        ];
    }

}