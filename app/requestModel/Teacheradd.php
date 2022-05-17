<?php 


namespace app\requestModel;

use app\core\Model;
use app\model\member;

class Teacheradd extends Model{

    public string $Account;

    public string $Name;
    
    public string $Password;

    public string $Title;

    public bool $IsAdmin;

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }            
        }
        $this->IsAdmin = true;
    }

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
            'Title' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX, 'max' => 100]
            ],
        ];
    }
}