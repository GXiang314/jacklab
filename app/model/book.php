<?php

namespace app\model;

use app\core\DbModel;

class book extends DbModel{

    public int $Id;
    public string $Title;
    public string $Publisher;
    public string $Time;
    public string $ISBN;
    public string $Image;


    public function table(): string
    {
        return 'book';
    }

    public function attributes(): array
    {
        return ['Title', 'Publisher', 'Time', 'ISBN', 'Image'];
    }

    public function save()
    {     
        $this->Time = date('Y-m-d h:i:s');   
        return parent::save();
    }

    public function rules(): array
    {            
        return [
            'Title' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>50],                
            ],
            'Publisher' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>50]
            ],
            'ISBN' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>50]
            ],
            'Image' => [self::RULE_REQUIRED],
        ];
    }

}