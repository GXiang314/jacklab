<?php 


namespace app\requestModel;

use app\core\Model;

class EmailValidate extends Model{


    public string $email;

    public string $token;

    public function rules(): array
    {
        return [            
            'email' => [self::RULE_REQUIRED],
            'token' => [self::RULE_REQUIRED],            
        ];
    }
}