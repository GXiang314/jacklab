<?php

namespace app\model;

use app\core\DbModel;
use app\services\MemberService;

class game_file extends DbModel{

    public int $Id;
    public string $Name;
    public string $Type;
    public $Size;
    public string $Url;
    public int $Game_record;



    public function table(): string
    {
        return 'game_file';
    }

    public function attributes(): array
    {
        return ['Name', 'Type', 'Size', 'Url', 'Game_record'];
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
            'Game_record' => [self::RULE_REQUIRED],
            
        ];
    }

}