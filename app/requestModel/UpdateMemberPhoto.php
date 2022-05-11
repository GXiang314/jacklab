<?php 


namespace app\requestModel;

use app\core\Model;

class UpdateMemberPhoto extends Model{


    public string $USER;

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
            'USER' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max'=>50]],
            'File' => [self::RULE_REQUIRED],
        ];
    }
}