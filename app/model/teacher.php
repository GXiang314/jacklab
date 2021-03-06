<?php

namespace app\model;

use app\core\DbModel;
use app\services\MemberService;

class teacher extends DbModel{

    public int $Id;
    public string $Name;
    public string $Image;
    public string $Title;
    public string $Introduction;
    public string $Account;


    public function table(): string
    {
        return 'teacher';
    }

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }            
        }
        $this->Id = MemberService::generateTeacherId();
        $this->Image = "\storage\member\man.png";
        $this->Introduction = '';
    }

    public function attributes(): array
    {
        return ['Id', 'Name', 'Image', 'Title', 'Introduction', 'Account'];
    }

    public function save()
    {
        $this->Id = MemberService::generateTeacherId();
        $this->Image = "\storage\member\man.png";
        $this->Introduction = '';
        return parent::save();
    }

    public function rules(): array
    {            
        return [
            'Name' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX,'max'=>20],                
            ],
            'Title' => [self::RULE_REQUIRED],
            'Account' => [self::RULE_REQUIRED],
        ];
    }

}