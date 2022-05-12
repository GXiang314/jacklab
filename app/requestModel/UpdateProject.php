<?php 


namespace app\requestModel;

use app\core\Model;

class UpdateProject extends Model{

    public int $Id;
    public string $Name;
    public string $Description;
    public string $CreateTime;
    public string $USER;
    public string $Deleted;
    public int $Proj_type;
    public array $Member;
    public array $Tag;


    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function rules(): array
    {
        return [            
            'Id' => [self::RULE_REQUIRED],
            'Name' => [self::RULE_REQUIRED],
            'Description' => [self::RULE_REQUIRED],
            'USER' => [self::RULE_REQUIRED],
            'Member' => [self::RULE_REQUIRED],
            'Proj_type' => [self::RULE_REQUIRED]         
        ];
    }
}