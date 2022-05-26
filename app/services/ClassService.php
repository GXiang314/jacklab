<?php

namespace app\services;

use app\core\DbModel;
use app\model\academic;
use app\model\classes;
use app\model\student;
use Exception;

class ClassService{

    public function __construct()
    {
        $this->install();
    }
    public function getAll()
    {
        $data = classes::get('classes');
        return $data;
    }

    public function getStudent($id)
    {
        $data = student::get('student',[
            'Class_Id' => $id
        ]);
        return $data;
    }

    public function add(string $name,int $academic)
    {
        try{
            classes::create('classes', [
                'Id' => $this->newId(),
                'Name' => $name,
                'Academic' => $academic,
            ]);
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        return 'success';
    }
    public function update($id, $name)
    {
        try{
            classes::update('classes', [
                'Name' => $name
            ],[
                'Id' => $id
            ]);
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        return 'success';
    }
    public function delete($id)
    {
        try{
            // delete student
            classes::delete('classes', [
                'Id' => $id
            ]);            
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        return 'success';
    }
    private function install(){
        if(classes::count('classes') == 0){
            $classArray = [
            '資訊應用菁英班四甲'=>4,
            '資訊應用菁英班五甲'=>4,
            '資訊管理科四甲'=>4,
            '資訊管理科五甲'=>4,
            '資訊應用菁英班三A'=>3,
            '資訊應用菁英班四A'=>3,
            '資訊管理系三A'=>3,
            '資訊管理系四A'=>3,
            '資訊管理系三1'=>2,
            '資訊管理系四1'=>2,
            '資訊管理系碩士班一1'=>1,
            '資訊管理系碩士班二1'=>1,
            '資訊管理系碩士在職專班一1'=>1,
            '資訊管理系碩士在職專班二1'=>1
            ];
            foreach($classArray as $key=>$value){
                classes::create('classes', [
                    'Id' => $this->newId(),
                    'Name' => $key,
                    'Academic_Id' => $value,
            ]);
            }
        }
    }

    private function newId()
    {
        $statement = academic::prepare("
            select Id from classes order by Id desc limit 1;
        ");
        $statement->execute();
        $id = $statement->fetch();
        return (isset($id['Id'])) ? $id['Id'] + 1 : 1;
    }
}