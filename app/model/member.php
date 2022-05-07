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

    public function save()
    {
        $this->Password = MemberService::hash($this->Password);
        $this->AuthToken = MemberService::generateAuthToken();
        $this->CreateTime = date('Y-m-d h:i:s');
        $this->IsAdmin = 0;
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