<?php 


namespace app\requestModel;

use app\core\Model;

class AddAlbum extends Model{


    public string $Title;    
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
            'Title' => [
                self::RULE_REQUIRED, 
                [self::RULE_MAX, 'max' => 20]
            ],
            'File' => [
                self::RULE_REQUIRED,
                [self::RULE_MAX, 'max' => 2000]
            ]
        ];
    }
}