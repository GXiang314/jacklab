<?php

namespace app\model;

use app\core\DbModel;

class album extends DbModel{

    public int $Id;
    public string $Name;
    public string $Image;
    public string $CreateTime;


    public function table(): string
    {
        return 'academic';
    }

    public function attributes(): array
    {
        return ['Name', 'Image', 'CreateTime'];
    }

    public function save()
    {     
        $this->CreateTime = date('Y-m-d h:i:s');   
        return parent::save();
    }

    public function rules(): array
    {            
        return [
            'Name' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>20],                
            ],
            'Image' => [self::RULE_REQUIRED],
        ];
    }

}