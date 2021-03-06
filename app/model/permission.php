<?php

namespace app\model;

use app\core\DbModel;

class permission extends DbModel{

    public int $Id;
    public string $Permission_group;    
    public string $Name;    
    public string $Url;    


    public function table(): string
    {
        return 'permission';
    }

    public function attributes(): array
    {
        return ['Permission_group','Name', 'Url'];
    }

    public function save()
    {        
        return parent::save();
    }

    public function rules(): array
    {            
        return [
            'Permission_group' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>20], 
            ],
            'Name' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>20],                
            ],
            'Url' => [
                self::RULE_REQUIRED
            ]
        ];
    }

}