<?php 


namespace app\requestModel;

use app\core\Model;

class UpdateName extends Model{


    public int $Id;

    public string $Name;

    public function rules(): array
    {
        return [            
            'Id' => [self::RULE_REQUIRED],
            'Name' => [self::RULE_REQUIRED],
        ];
    }
}