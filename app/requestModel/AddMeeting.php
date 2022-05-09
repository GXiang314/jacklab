<?php 


namespace app\requestModel;

use app\core\Model;

class AddMeeting extends Model{


    public string $Title;
    public string $Content;
    public string $Time;
    public string $Place;
    public string $USER;
    public array $Member;
    public array $Tag;
    public $Files;

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        $this->Files = $_FILES['Files'] ?? null;
    }

    public function rules(): array
    {
        return [            
            'Title' => [self::RULE_REQUIRED],
            'Content' => [self::RULE_REQUIRED],
            'Time' => [self::RULE_REQUIRED],
            'Place' => [self::RULE_REQUIRED],
            'USER' => [self::RULE_REQUIRED],
            'Member' => [self::RULE_REQUIRED],
        ];
    }
}