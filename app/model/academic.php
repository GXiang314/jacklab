<?php

namespace app\model;

use app\core\DbModel;

class academic extends DbModel{

    public int $Id;
    public string $Name;    


    public static function table(): string
    {
        return 'academic';
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