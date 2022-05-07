<?php 


namespace app\requestModel;

use app\core\Model;

class Useradd extends Model{

    public string $Account;

    public string $Name;

    public int $Class_Id;

    public int $Role_Id;

    public function rules(): array
    {
        return [
            'Account' => [self::RULE_REQUIRED,self::RULE_EMAIL,[self::RULE_MAX,'max'=>50]],
            'Name' => [self::RULE_REQUIRED],
            'Class_Id' => [self::RULE_REQUIRED],
            'Role_Id' => [self::RULE_REQUIRED]
        ];
    }
}