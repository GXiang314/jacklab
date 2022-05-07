<?php

namespace app\model;

use app\core\DbModel;

class role_permission extends DbModel{

    public int $Id;
    public int $Role_Id;
    public int $Permission_Id;


    public function table(): string
    {
        return 'role_permission';
    }

    public function attributes(): array
    {
        return ['Role_Id', 'Permission_Id'];
    }

    public function save()
    {        
        return parent::save();
    }

    public function rules(): array
    {            
        return [
            'Role_Id' => [
                self::RULE_REQUIRED,
            ],
            'Permission_Id' => [
                self::RULE_REQUIRED,
            ]
        ];
    }

}