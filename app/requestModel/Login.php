<?php 


namespace app\requestModel;

use app\core\Model;

class Login extends Model{


    public string $Account;

    public string $Password;

    public function rules(): array
    {
        return [            
            'Account' => [self::RULE_REQUIRED],
            'Password' => [self::RULE_REQUIRED],            
        ];
    }
}