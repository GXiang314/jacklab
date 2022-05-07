<?php

namespace app\model;

use app\core\DbModel;

class meeting_tag extends DbModel{

    public int $Id;
    public string $Name;    
    public int $Meet_Id;


    public function table(): string
    {
        return 'meeting_tag';
    }

    public function attributes(): array
    {
        return ['Name', 'Meet_Id'];
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
            'Meet_Id' => [
                self::RULE_REQUIRED,
            ]
        ];
    }

}