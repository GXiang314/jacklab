<?php

namespace app\model;

use app\core\DbModel;

class proj_file extends DbModel{

    public int $Id;
    public string $Name;
    public string $Type;
    public $Size;
    public string $Url;
    public int $Proj_record;



    public function table(): string
    {
        return 'proj_file';
    }

    public function attributes(): array
    {
        return ['Name', 'Type', 'Size', 'Url', 'Proj_record'];
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
                [self::RULE_MAX,'max'=>200],                
            ],
            'Type' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>50],
            ],
            'Url' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>2000],
            ],            
            'Proj_record' => [self::RULE_REQUIRED],
            
        ];
    }

}