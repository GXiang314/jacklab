<?php 


namespace app\requestModel;

use app\core\Model;

class ChangePassword extends Model{


    public string $oldpassword;

    public string $password;

    public string $password_confirmation;

    public function rules(): array
    {
        return [            
            'oldpassword' => [self::RULE_REQUIRED],
            'password' => [self::RULE_REQUIRED],
            'password_confirmation' => [
                self::RULE_REQUIRED,
                [self::RULE_MATCH,'match' => 'password']
            ]
        ];
    }
}