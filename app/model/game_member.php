<?php

namespace app\model;

use app\core\DbModel;

class game_member extends DbModel{

    public int $Id;
    public int $Game_record;
    public int $Student_Id;    


    public static function table(): string
    {
        return 'game_member';
    }

    public function attributes(): array
    {
        return ['Game_record', 'Student_Id'];
    }

    public function save()
    {        
        return parent::save();
    }

    public function rules(): array
    {            
        return [
            'Game_record' => [
                self::RULE_REQUIRED,
            ],
            'Student_Id' => [
                self::RULE_REQUIRED,                               
            ],
            
        ];
    }

}