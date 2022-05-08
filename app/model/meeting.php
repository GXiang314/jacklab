<?php

namespace app\model;

use app\core\DbModel;

class meeting extends DbModel{

    public int $Id;
    public string $Title;
    public string $Content;
    public string $Time;
    public string $Place;
    public string $Uploader;
    public string $Deleted;



    public static function table(): string
    {
        return 'meeting';
    }

    public function attributes(): array
    {
        return ['Id', 'Title', 'Content', 'Time', 'Place', 'Uploader'];
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
            'Title' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>50],                
            ],
            'Content' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>2000],
            ],
            'Time' => [
                self::RULE_REQUIRED,
            ],
            'Place' => [
                self::RULE_REQUIRED],
                [self::RULE_MAX,'max'=>20],
            'Uploader' => [
                self::RULE_REQUIRED],
                [self::RULE_MAX,'max'=>50],
            
            
        ];
    }

}