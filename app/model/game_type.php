<?php

namespace app\model;

use app\core\DbModel;

class game_type extends DbModel{

    public int $Id;
    public string $Name;    


    public function table(): string
    {
        return 'game_type';
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