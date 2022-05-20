<?php

namespace app\services;

use app\core\DbModel;
use app\model\lab_info;
use Exception;

class LabinfoService{

    public function __construct()
    {
        //$this->install();
    }
    public function getAll()
    {
        $data = lab_info::get('lab_info');
        return $data;
    }

    public function getOne($id)
    {
        $data = lab_info::findOne('lab_info',[
            'Id' => $id
        ]);
        return $data;
    }

    public function add(string $title,string $content)
    {
        try{
            lab_info::create('lab_info', [
                'Title' => $title,
                'Content' => $content,
            ]);
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        return 'success';
    }
    public function update($id, $title, $content)
    {
        try{
            lab_info::update('lab_info', [
                'Title' => $title,
                'Content' => $content
            ],[
                'Id' => $id
            ]);
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        return 'success';
    }
    public function delete($idList)
    {
        try{
            $idList = explode(',', $idList);
            foreach($idList as $id){
                lab_info::delete('lab_info', [
                    'Id' => $id
                ]);   
            }
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        return 'success';
    }
    private function install(){
        if(lab_info::count('lab_info') == 0){
            $infoArray = [
            '哈哈是研究室介紹'=>"哈哈研究室介紹",            
            ];
            foreach($infoArray as $key=>$value){
                lab_info::create('lab_info', [
                    'Title' => $key,
                    'Content' => $value,
            ]);
            }
        }
    }

    // private function newId()
    // {
    //     $statement = lab_info::prepare("
    //         select Id from lab_info order by Id desc limit 1;
    //     ");
    //     $statement->execute();
    //     $id = $statement->fetch();
    //     return (isset($id['Id'])) ? $id['Id'] + 1 : 1;
    // }
}