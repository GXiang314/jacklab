<?php 


namespace app\requestModel;

use app\core\Model;
use app\model\member;

class Teacheradd extends Model{

    public string $Account;

    public string $Name;
    
    public string $Password;

    public int $Role_Id;

    public function rules(): array
    {
        return [
            'Account' => [
                self::RULE_REQUIRED,
                self::RULE_EMAIL,
                [self::RULE_MAX,'max'=>50],
                [self::RULE_UNIQUE,'class'=>member::class]
            ],
            'Name' => [self::RULE_REQUIRED],
            'Role_Id' => [self::RULE_REQUIRED]
        ];
    }
}