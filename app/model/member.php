<?php

namespace app\model;

use app\core\DbModel;
use app\services\MemberService;

class member extends DbModel{

    public string $Account;
    public string $Password;
    public string $AuthToken;
    public string $CreateTime;
    public bool $IsAdmin;

    public function table(): string
    {
        return 'member';
    }

    public function attributes(): array
    {
        return ['Account', 'Password', 'AuthToken', 'CreateTime', 'IsAdmin'];
    }

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }            
        }
        $this->Password = MemberService::generatePassword();
        $this->AuthToken = (isset($this->AuthToken))? '' :MemberService::generateAuthToken();
        $this->CreateTime = date('Y-m-d h:i:s');
        $this->IsAdmin = (isset($this->IsAdmin))? $this->IsAdmin : false;
    }

    public function save()
    {        
        $this->Password = MemberService::hash($this->Password);
        return parent::save();
    }

    public function rules(): array
    {            
        return [
            'Account' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>50],
                [self::RULE_UNIQUE,'class'=>self::class]
            ],
            'Password' => [self::RULE_REQUIRED],

        ];
    }

}