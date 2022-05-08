<?php

namespace app\model;

use app\core\DbModel;

class author extends DbModel{

    public int $Id;
    public int $Book_Id;
    public string $Account;    


    public static function table(): string
    {
        return 'author';
    }

    public function attributes(): array
    {
        return ['Book_Id', 'Account'];
    }

    public function save()
    {        
        return parent::save();
    }

    public function rules(): array
    {            
        return [            
            'Book_Id' => [
                self::RULE_REQUIRED,
            ],
            'Account' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>50],                
            ],
        ];
    }

}