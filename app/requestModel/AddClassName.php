<?php 


namespace app\requestModel;

use app\core\Model;

class AddClassName extends Model{


    public string $Name;

    public int $Academic_Id;

    public function rules(): array
    {
        return [            
            'Name' => [self::RULE_REQUIRED],
            'Academic_Id' => [self::RULE_REQUIRED],
        ];
    }
}