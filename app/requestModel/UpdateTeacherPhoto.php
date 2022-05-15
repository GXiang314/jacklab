<?php 


namespace app\requestModel;

use app\core\Model;

class UpdateTeacherPhoto extends Model{


    public int $Id;

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
            'Id' => [self::RULE_REQUIRED],
            'File' => [self::RULE_REQUIRED],
        ];
    }
}