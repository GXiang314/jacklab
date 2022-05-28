<?php

namespace app\model;

use app\core\DbModel;

class permission_group extends DbModel{

    public string $Id;
    public string $Name;    


    public function table(): string
    {
        return 'permission_group';
    }

    public function attributes(): array
    {
        return ['Id','Name'];
    }

    public function save()
    {        
        return parent::save();
    }

    public function rules(): array
    {            
        return [
            'Id' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>20], 
            ],
            'Name' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>20],                
            ],
        ];
    }

}