<?php

namespace app\model;

use app\core\DbModel;
use DateTime;

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

    public function rules(): array
    {            
        return [
            'Account' => [self::RULE_REQUIRED,[self::RULE_MAX,'max'=>50]],
            'Password' => [self::RULE_REQUIRED],

        ];
    }

}