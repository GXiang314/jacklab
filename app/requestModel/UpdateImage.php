<?php 


namespace app\requestModel;

use app\core\Model;

class UpdateImage extends Model{


    public int $Id;

    public $Image;

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        $this->Image = $_FILES['Image'] ?? null;
    }

    public function rules(): array
    {
        return [            
            'Id' => [self::RULE_REQUIRED],
            'Image' => [self::RULE_REQUIRED],
        ];
    }
}