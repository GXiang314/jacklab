<?php 


namespace app\requestModel;

use app\core\Model;

class UpdateTeacherInfo extends Model{

    public int $Id;

    public string $Name;

    public string $Title;

    public string $Introduction;

    public function rules(): array
    {
        return [            
            'Id' => [self::RULE_REQUIRED],
            'Name' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX, 'max' => 20]
        ],
            'Title' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX, 'max' => 100]
            ],
            'Introduction' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX, 'max' => 5000]
            ]
        ];
    }
}