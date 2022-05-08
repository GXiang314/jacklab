<?php

namespace app\core;

use Exception;

abstract class DbModel extends Model
{

    abstract public function table(): string;

    abstract public function attributes(): array;

    public function save()
    {
        try {
            $tablename = $this->table();
            $attributes = $this->attributes();
            $params = array_map(fn ($attr) => ":$attr", $attributes);
            $statement = self::prepare("
            insert into $tablename(" . implode(',', $attributes) . ")        
            values(" . implode(',', $params) . "
            );");
            foreach ($attributes as $attr) {
                $statement->bindValue(":$attr", $this->{$attr});
            }
            $statement->execute();
        } catch (Exception) {
            return false;
        }
        return true;
    }

    public static function create($tableName, array $inserData){
        try{
            $insertKey = implode(',', array_keys($inserData));
            $insertValue = implode(',', array_map(fn($attr) => "'$attr'", $inserData));
            $statement = self::prepare("insert into $tableName($insertKey) values($insertValue);");
            $statement->execute();
        }catch(Exception){
            return false;
        }
        return true;
    }
   
    public static function get($tableName, array $where = null, array $except = null) // not in ['account'=>'123456789@gmail.com','name'=>'asdasdasd'] ...
    {
        try {
            $statement = self::prepare("select * from $tableName;");
            if ($where) {
                $attributes = array_keys($where);
                $sql = implode(' and ', array_map(fn ($attr) => "$attr in (:$attr)", $attributes));
                $statement = self::prepare("select * from $tableName where $sql;");
                foreach ($except as $key => $value) {
                    $statement->bindValue(":$key", $value);
                }
            }

            if ($except) {
                $attributes = array_keys($except);
                $sql = implode(' and ', array_map(fn ($attr) => "$attr not in (:$attr)", $attributes));
                $statement = self::prepare("select * from $tableName where $sql;");
                foreach ($except as $key => $value) {
                    $statement->bindValue(":$key", $value);
                }
            }

            $statement->execute();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function findOne($tableName, array $where) //where ['account'=>'123456789@gmail.com','name'=>'asdasdasd'] ...
    {
        try {
            $attributes = array_keys($where);
            $sql = implode(' and ', array_map(fn ($attr) => "$attr = :$attr", $attributes));
            $statement = self::prepare("select * from $tableName where $sql;");
            foreach ($where as $key => $value) {
                $statement->bindValue(":$key", $value);
            }
            $statement->execute();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    public static function update($tableName, array $setColumn, array $where = []) //set['password'=>password] where ['account'=>'123456789@gmail.com','name'=>'asdasdasd'] ...
    {
        try {
            $updateKey = array_keys($setColumn);
            $whereKey = array_keys($where);
            $update_sql = implode(', ', array_map(fn ($attr) => "$attr = :$attr ", $updateKey));
            $where_sql = ($where) ? ' where ' . implode(' and ', array_map(fn ($attr) => "$attr = :$attr", $whereKey)) : '';
            $statement = self::prepare("update $tableName set $update_sql $where_sql;");
            foreach ($setColumn as $key => $value) {
                $statement->bindValue(":$key", $value);
            }
            foreach ($where as $key => $value) {
                $statement->bindValue(":$key", $value);
            }
            $statement->execute();
        } catch (Exception) {
            return false;
        }
        return true;
    }

    public static function delete($tableName, array $where = []) //where ['account'=>'123456789@gmail.com','name'=>'asdasdasd'] ...
    {
        try {
            $attributes = array_keys($where);
            $sql = ($where) ? ' where ' . implode(' and ', array_map(fn ($attr) => "$attr = :$attr", $attributes)) : '';
            $statement = self::prepare("delete from $tableName $sql;");
            foreach ($where as $key => $value) {
                $statement->bindValue(":$key", $value);
            }
            $statement->execute();
        } catch (Exception) {
            return false;
        }
        return true;
    }

    public static function count($tableName){
        $statement = self::prepare("select count(*) as c from $tableName;");
        $statement->execute();
        return $statement->fetch(\PDO::FETCH_ASSOC)['c'];
    }

    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}
