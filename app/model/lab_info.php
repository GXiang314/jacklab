<?php

namespace app\model;

use app\core\DbModel;

class lab_info extends DbModel{

    public int $Id;
    public string $Title;    
    public string $Content;


    public static function table(): string
    {
        return 'lab_info';
    }

    public function attributes(): array
    {
        return ['Title', 'Content'];
    }

    public function save()
    {        
        return parent::save();
    }

    public function rules(): array
    {            
        return [
            'Title' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>20],                
            ],
            'Content' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>2000],                
            ]
        ];
    }

}