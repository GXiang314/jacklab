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

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }            
        }        
        $this->CreateTime = date('Y-m-d h:i:s');   
    }

    public function save()
    {     
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