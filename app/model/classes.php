<?php

namespace app\model;

use app\core\DbModel;

class classes extends DbModel{

    public int $Id;
    public string $Name;    
    public int $Academic_Id;


    public static function table(): string
    {
        return 'academic';
    }

    public function attributes(): array
    {
        return ['Name', 'Academic_Id'];
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
            'Academic_Id' => [
                self::RULE_REQUIRED,
            ]
        ];
    }

}