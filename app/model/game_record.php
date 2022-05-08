<?php

namespace app\model;

use app\core\DbModel;

class game_record extends DbModel{

    public int $Id;
    public string $Name;
    public string $Game_group;
    public string $Ranking;
    public string $Game_time;
    public string $Deleted;
    public string $Uploader;
    public int $Game_type;



    public function table(): string
    {
        return 'game_record';
    }

    public function attributes(): array
    {
        return ['Id', 'Name', 'Game_group', 'Ranking', 'Game_time', 'Uploader', 'Game_type'];
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
        // $this->Id =         
        return parent::save();
    }

    public function rules(): array
    {            
        return [
            'Id' => [
                self::RULE_REQUIRED
            ],
            'Name' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>50],                
            ],
            'Game_group' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>100],
            ],
            'Ranking' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>20],
            ],
            'Game_time' => [self::RULE_REQUIRED],
            'Uploader' => [
                self::RULE_REQUIRED],
                [self::RULE_MAX,'max'=>50],
            'Game_type' => [self::RULE_REQUIRED],
            
        ];
    }

}