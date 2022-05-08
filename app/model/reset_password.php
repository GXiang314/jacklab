<?php

namespace app\model;

use app\core\DbModel;

class reset_password extends DbModel{

    public int $Id;
    public string $Account;    
    public string $Update_at;    


    public function table(): string
    {
        return 'reset_password';
    }

    public function attributes(): array
    {
        return ['Account', 'Update_at', 'Code'];
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
        ];
    }

}