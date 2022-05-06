<?php 

namespace app\core;

abstract class DbModel extends Model{

    abstract public function table(): string;

    abstract public function attributes(): array;
    
    public function save()
    {
        $tablename = $this->table();
        $attributes = $this->attributes();
        $params = array_map(fn($attr)=>":$attr", $attributes);
        $statement = self::prepare("
        insert into $tablename(" . implode(',',$attributes) . ")        
        values(" . implode(',',$params). "
        );");
        foreach($attributes as $attr){
            $statement->bindValue(":$attr",$this->{$attr});
        }
        var_dump($statement->execute());


    }

    public static function prepare($sql){
        return Application::$app->db->pdo->prepare($sql);
    }
}