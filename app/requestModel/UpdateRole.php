<?php 


namespace app\requestModel;

use app\core\Model;

class UpdateRole extends Model{


    public int $Id;

    public array $Permission;

    public function rules(): array
    {
        return [            
            'Id' => [self::RULE_REQUIRED],
            'Permission' => [self::RULE_REQUIRED],
        ];
    }
}