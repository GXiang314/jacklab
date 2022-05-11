<?php 


namespace app\requestModel;

use app\core\Model;

class AddProject_record extends Model{


    public string $Remark;
    public string $CreateTime;
    public string $Deleted;
    public string $USER;
    public int $Project_Id;
    public $File;

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        $this->File = $_FILES['File'] ?? null;
    }

    public function rules(): array
    {
        return [            
            'Remark' => [self::RULE_REQUIRED],
            'USER' => [self::RULE_REQUIRED],
            'Project_Id' => [self::RULE_REQUIRED],
            'File' => [self::RULE_REQUIRED]
        ];
    }
}