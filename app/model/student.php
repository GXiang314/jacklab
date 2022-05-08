<?php

namespace app\model;

use app\core\DbModel;
use app\services\MemberService;

class student extends DbModel{

    public int $Id;
    public string $Name;
    public string $Image;
    public string $Introduction;
    public int $Class_Id;
    public string $Account;



    public function table(): string
    {
        return 'student';
    }

    public function attributes(): array
    {
        return ['Id', 'Name', 'Image', 'Introduction', 'Class_Id', 'Account'];
    }

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }            
        }
        $this->Id = MemberService::generateStudentId($this->Class_Id);
        $this->Image = 'member/man.png';
        $this->Introduction = '';
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
            'Password' => [self::RULE_REQUIRED],
            'Class_Id' => [self::RULE_REQUIRED],
            'Account' => [self::RULE_REQUIRED],
        ];
    }

}