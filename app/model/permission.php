<?php

namespace app\model;

use app\core\DbModel;

class permission extends DbModel{

    public int $Id;
    public string $Name;    
    public string $Url;    


    public static function table(): string
    {
        return 'permission';
    }

    public function attributes(): array
    {
        return ['Name', 'Url'];
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
            'Url' => [
                self::RULE_REQUIRED
            ]
        ];
    }

}