<?php

namespace app\model;

use app\core\DbModel;

class member_role extends DbModel{

    public int $Id;
    public string $Account;    
    public int $Role_Id;


    public static function table(): string
    {
        return 'member_role';
    }

    public function attributes(): array
    {
        return ['Account', 'Role_Id'];
    }

    public function save()
    {        
        return parent::save();
    }

    public function rules(): array
    {            
        return [
            'Account' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>50],                
            ],
            'Role_Id' => [
                self::RULE_REQUIRED,
            ]
        ];
    }

}