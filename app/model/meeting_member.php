<?php

namespace app\model;

use app\core\DbModel;

class meeting_member extends DbModel{

    public int $Id;
    public int $Meet_Id;
    public string $Account;    


    public function table(): string
    {
        return 'meeting_member';
    }

    public function attributes(): array
    {
        return ['Meet_Id', 'Account'];
    }

    public function save()
    {        
        return parent::save();
    }

    public function rules(): array
    {            
        return [
            'Meet_Id' => [
                self::RULE_REQUIRED,
            ],
            'Account' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>50],                
            ],
            
        ];
    }

}