<?php 


namespace app\requestModel;

use app\core\Model;

class UpdateBook extends Model{

    public int $Id;
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
            'Id' => [
                self::RULE_REQUIRED
            ],
            'Title' => [
                self::RULE_REQUIRED, 
                [self::RULE_MAX, 'max' => 50]
            ],
            'Publisher' => [
                self::RULE_REQUIRED, 
                [self::RULE_MAX, 'max' => 50]
            ],
            'Time' => [self::RULE_REQUIRED],
            'ISBN' => [self::RULE_REQUIRED],
            'Authors' => [self::RULE_REQUIRED],
        ];
    }
}