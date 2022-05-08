<?php 


namespace app\requestModel;

use app\core\Model;

class AddName extends Model{


    public string $Name;

    public function rules(): array
    {
        return [            
            'Name' => [self::RULE_REQUIRED],
        ];
    }
}