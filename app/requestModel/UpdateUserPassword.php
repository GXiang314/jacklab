<?php 


namespace app\requestModel;

use app\core\Model;

class UpdateUserPassword extends Model{


    public string $account;

    public string $password;

    public string $password_confirm;

    public function rules(): array
    {
        return [            
            'account' => [self::RULE_REQUIRED],
            'password' => [self::RULE_REQUIRED],
            'password_confirm' => [
                self::RULE_REQUIRED,
                [self::RULE_MATCH,'match' => 'password']
            ]
        ];
    }
}