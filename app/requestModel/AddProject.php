<?php 


namespace app\requestModel;

use app\core\Model;

class AddProject extends Model{


    public string $Name;
    public string $Description;
    public string $CreateTime;
    public string $USER;
    public string $Deleted;
    public int $Proj_type;
    public array $Tag;
    public array $Member;


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
            'Name' => [self::RULE_REQUIRED],
            'Description' => [self::RULE_REQUIRED],
            'USER' => [self::RULE_REQUIRED],
            'Member' => [self::RULE_REQUIRED],
            'Proj_type' => [self::RULE_REQUIRED]         
        ];
    }
}