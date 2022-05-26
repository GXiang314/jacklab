<?php

namespace app\services;

use app\core\DbModel;
use app\model\academic;
use app\model\classes;
use Exception;

class AcademicService{

    public function __construct()
    {
        $this->install();
    }
    public function getAll()
    {
        $data = academic::get('academic');
        return $data;
    }

    public function getClass($id)
    {
        $data = classes::get('classes',[
            'Academic_Id' => $id
        ]);
        return $data;
    }

    public function add($name)
    {
        try{
            academic::create('academic', [
                'Id' => $this->newId(),
                'Name' => $name
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
            academic::update('academic', [
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
            classes::delete('classes', [
                'Academic_Id' => $id
            ]);
            academic::delete('academic', [
                'Id' => $id
            ]);
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        return 'success';
    }
    private function install(){
        if(academic::count('academic') == 0){
            $acadmicArray = [
            '碩士班',
            '大學部',
            '二技部',
            '五專部'
            ];
            foreach($acadmicArray as $value){
                academic::create('academic', [
                    'Id' => $this->newId(),
                    'Name' => $value
                ]);
            }
        }
    }
    private function newId()
    {
        $statement = academic::prepare("
            select Id from academic order by Id desc limit 1;
        ");
        $statement->execute();
        $id = $statement->fetch();
        return (isset($id['Id'])) ? $id['Id'] + 1 : 1;
    }
}