<?php

namespace app\services;

use app\core\DbModel;
use app\core\Exception\InternalServerErrorException;
use app\model\lab_info;
use Exception;

class LabinfoService{

    public function __construct()
    {
        //$this->install();
    }

    public function getAll($page = 1, $search = null)
    {
        try{
            $search = $this->addSlashes($search);
            $statement = DbModel::prepare("
            select 
                l.* 
            from 
                lab_info as l ".
            (($search != null)?
            " Where 
                l.Title like :search  or 
                l.Content like :search  
            " : "")
            .
            " Order by 
                l.Id desc 
            "
            .
            " limit " . (($page - 1) * $_ENV['PAGE_ITEM_NUM']) . ", " . ($_ENV['PAGE_ITEM_NUM']) . ";"
            );
            if ($search != null){
                $statement->bindValue(':search', "%".$search."%");
            }
            $statement->execute();
            $data['list'] = $statement->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        $data['page'] = $this->getAllInfoPage($search);
        $statement = null;
        return $data;
    }

    public function getOne($id)
    {
        $data = lab_info::findOne('lab_info',[
            'Id' => $id
        ]);
        return $data;
    }

    public function getAllInfoPage($search = null)
    {
        try{
            $search = $this->addSlashes($search);
            $statement =  DbModel::prepare("
            select count(*) from lab_info "
            .
            (($search != null) ?
                " 
            where 
            Title like :search  or
            Content like :search 
            " : ""
            ));
            if ($search != null){
                $statement->bindValue(':search', "%".$search."%");
            }
            $statement->execute();
            $count = $statement->fetchColumn();
        } catch (Exception) {
            throw new InternalServerErrorException();
        }        
        $page = ceil((float)$count / $_ENV['PAGE_ITEM_NUM']);
        $statement = null;
        return $page == 0 ? 1 : $page;
    }

    public function add(string $title,string $content)
    {
        lab_info::create('lab_info', [
            'Title' => $title,
            'Content' => $content,
        ]);
        return 'success';
    }
    public function update($id, $title, $content)
    {
        lab_info::update('lab_info', [
            'Title' => $title,
            'Content' => $content
        ],[
            'Id' => $id
        ]);
        return 'success';
    }
    public function delete($idList)
    {
        $idList = explode(',', $idList);
        foreach($idList as $id){
            lab_info::delete('lab_info', [
                'Id' => $id
            ]);   
        }
        return 'success';
    }
    private function install(){
        if(lab_info::count('lab_info') == 0){
            $infoArray = [
            '????????????????????????'=>"?????????????????????",            
            ];
            foreach($infoArray as $key=>$value){
                lab_info::create('lab_info', [
                    'Title' => $key,
                    'Content' => $value,
            ]);
            }
        }
        
    }

    public function addSlashes($string = null)
    {
        return  empty($string) ? $string : addslashes($string);
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