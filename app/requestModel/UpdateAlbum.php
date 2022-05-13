<?php 


namespace app\requestModel;

use app\core\Model;

class UpdateAlbum extends Model{


    public int $Id;    
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
            'Id' => [self::RULE_REQUIRED],     
            'Title' => [
                self::RULE_REQUIRED, 
                [self::RULE_MAX, 'max' => 20]
            ]
        ];
    }
}