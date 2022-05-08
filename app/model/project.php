<?php

namespace app\model;

use app\core\DbModel;

class project extends DbModel{

    public int $Id;
    public string $Name;
    public string $Description;
    public string $CreateTime;
    public string $Creater;
    public int $Proj_type;



    public static function table(): string
    {
        return 'project';
    }

    public function attributes(): array
    {
        return ['Name', 'Description', 'CreateTime', 'Creater', 'Proj_type'];
    }

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }            
        }        
    }

    public function save()
    {        
        //get extension
        return parent::save();
    }

    public function rules(): array
    {            
        return [            
            'Name' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>50],                
            ],
            'Description' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>2000],
            ],
            'CreateTime' => [
                self::RULE_REQUIRED,
            ],
            'Creater' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>50],
            ],       
                     
            'Proj_record' => [self::RULE_REQUIRED],
            
        ];
    }

}