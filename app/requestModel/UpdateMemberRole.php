<?php 


namespace app\requestModel;

use app\core\Model;

class UpdateMemberRole extends Model{


    public string $Account;

    public int $Role;

    public function rules(): array
    {
        return [            
            'Account' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max'=>50]],
            'Role' => [self::RULE_REQUIRED],
        ];
    }
}