<?php 


namespace app\requestModel;

use app\core\Model;

class UpdateStudentClass extends Model{


    public string $Account;

    public int $Class;

    public function rules(): array
    {
        return [            
            'Account' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max'=>50]],
            'Class' => [self::RULE_REQUIRED],
        ];
    }
}