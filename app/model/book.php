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


    public static function table(): string
    {
        return 'book';
    }

    public function attributes(): array
    {
        return ['Title', 'Publisher', 'Time', 'ISBN', 'Image'];
    }

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }            
        }        
        $this->Time = date('Y-m-d h:i:s');   
    }

    public function save()
    {     
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