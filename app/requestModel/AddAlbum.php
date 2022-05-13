<?php 


namespace app\requestModel;

use app\core\Model;

class AddAlbum extends Model{


    public string $Title;    
    public $Image;

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        $this->Image = $_FILES['File'] ?? null;
    }

    public function rules(): array
    {
        return [            
            'Title' => [
                self::RULE_REQUIRED, 
                [self::RULE_MAX, 'max' => 20]
            ],
            'Image' => [
                self::RULE_REQUIRED
            ]
        ];
    }
}