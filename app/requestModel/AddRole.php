<?php 


namespace app\requestModel;

use app\core\Model;
use app\model\role;

class AddRole extends Model{


    public string $Name;

    public array $Permission;

    public function rules(): array
    {
        return [            
            'Name' => [self::RULE_REQUIRED, 
            [self::RULE_MAX, 'max'=>20],
            [self::RULE_UNIQUE, 'class' => role::class]
        ],
            'Permission' => [self::RULE_REQUIRED],
        ];
    }
}