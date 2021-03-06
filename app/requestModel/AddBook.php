<?php 


namespace app\requestModel;

use app\core\Model;
use app\model\book;

class AddBook extends Model{


    public string $Title;
    public string $Publisher;
    public string $Time;
    public string $ISBN;
    public array $Authors;
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
            'Title' => [
                self::RULE_REQUIRED, 
                [self::RULE_MAX, 'max' => 100]
            ],
            'Publisher' => [
                self::RULE_REQUIRED, 
                [self::RULE_MAX, 'max' => 50]
            ],
            'Time' => [self::RULE_REQUIRED],
            'ISBN' => [
                self::RULE_REQUIRED, 
                [self::RULE_UNIQUE, 'class' => book::class]
            ],
            'Authors' => [self::RULE_REQUIRED],
            'Image' => [self::RULE_REQUIRED],
        ];
    }
}