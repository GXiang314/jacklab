<?php 


namespace app\requestModel;

use app\core\Model;

class AddGame_record extends Model{


    public string $Name;
    public string $Game_group;
    public string $Ranking;
    public string $Game_time;
    public string $USER;
    public int $Game_type;
    public array $Member;
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
            'Name' => [self::RULE_REQUIRED],
            'Game_group' => [self::RULE_REQUIRED],
            'Ranking' => [self::RULE_REQUIRED],
            'Game_time' => [self::RULE_REQUIRED],
            'USER' => [self::RULE_REQUIRED],
            'Game_type' => [self::RULE_REQUIRED],
            'Member' => [self::RULE_REQUIRED],
        ];
    }
}