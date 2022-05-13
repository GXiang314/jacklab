<?php 


namespace app\requestModel;

use app\core\Model;

class UpdateLabinfo extends Model{


    public int $Id;

    public string $Title;

    public string $Content;

    public function rules(): array
    {
        return [            
            'Id' => [self::RULE_REQUIRED],
            'Title' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX, 'max' => 20]
            ],
            'Content' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max' => 2000]
            ]
        ];
    }
}