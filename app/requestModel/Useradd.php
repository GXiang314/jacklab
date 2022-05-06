<?php 


namespace app\requestModel;

use app\core\Model;

class Useradd extends Model{

    public string $account;

    public string $name;

    public int $class_Id;

    public int $role_Id;

    public function table(): string{
        return 'member';
    }

    public function rules(): array
    {
        return [
            'account' => [self::RULE_REQUIRED,self::RULE_EMAIL,[self::RULE_MAX,'max'=>50]],
            'name' => [self::RULE_REQUIRED],
            'class_Id' => [self::RULE_REQUIRED],
            'role_Id' => [self::RULE_REQUIRED]
        ];
    }
}