<?php

namespace app\model;

use app\core\DbModel;

class role_permission_group extends DbModel{

    public int $Id;
    public int $Role_Id;
    public int $Permission_group;


    public function table(): string
    {
        return 'role_permission';
    }

    public function attributes(): array
    {
        return ['Role_Id', 'Permission_group'];
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
            'Permission_group' => [
                self::RULE_REQUIRED,
            ]
        ];
    }

}