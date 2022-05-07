<?php 

namespace app\core;

use Exception;

abstract class DbModel extends Model{

    abstract public function table(): string;

    abstract public function attributes(): array;
    
    public function save()
    {
        try{
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
            $statement->execute();
        }catch(Exception){
            return false;
        }        
        return true;
    }

    public static function prepare($sql){
        return Application::$app->db->pdo->prepare($sql);
    }
    
}