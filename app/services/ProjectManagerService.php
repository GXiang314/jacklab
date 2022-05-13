<?php

namespace app\services;

use app\core\DbModel;
use app\model\proj_tag;
use app\model\proj_type;
use app\model\project;
use Exception;

class ProjectManagerService{

    public function __construct()
    {
        $this->install();
    }
    public function getAll()
    {
        $data = proj_type::get('proj_type');
        return $data;
    }

    public function getProject($id = '%', $page = 1, $search = null)
    {
        $statement = project::prepare("
        SELECT DISTINCT
            p.Id,
            p.Name,
            p.Description,
            p.Creater,
            p.CreateTime,
        CASE
            s.`Name` 
            WHEN s.`Name` THEN
            s.`Name` ELSE t.NAME 
        END AS Creater_name 
        FROM
            project AS p
            LEFT JOIN student AS s ON s.Account = p.Creater
            LEFT JOIN teacher AS t ON t.Account = p.Creater 
        	LEFT JOIN proj_tag AS pt ON pt.Project_Id = p.Id 
        WHERE
            p.Proj_type like '{$id}'  and (
                ISNULL(p.Deleted) or p.Deleted like ''	
                ) ".
            ((!empty($search))
            ? 
            "and (p.NAME like '%$search%'
             or p.Description like '%$search%'
             or p.Creater like '%$search%'
             or p.CreateTime like '%$search%'
             or s.Name like '%$search%'
             or t.Name like '%$search%'
             or pt.Name like '%$search%'
             )"
             :' ')            
            ." 
        ORDER BY
            p.CreateTime DESC "
            . 
        " limit ".(($page-1)*10).", ".($page*10).";");
        $statement->execute();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (!empty($data)) {
            $index = 0;
            foreach ($data as $row) {
                $statement = proj_tag::prepare("
                SELECT
                    * 
                FROM
                    proj_tag AS pt 
                WHERE
                    pt.Project_Id = '{$row['Id']}';");
                $statement->execute();
                $tag = $statement->fetchAll(\PDO::FETCH_ASSOC);
                if (!empty($tag)) {
                    $data[$index]['Tag'] = $tag;
                } else {
                    $data[$index]['Tag'] = [];
                }
                $index++;
            }
        }
        return $data;
        return $data;
    }

    public function add(string $name)
    {
        try{
            proj_type::create('proj_type', [
                'Id' => $this->newId(),
                'Name' => $name,
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
            proj_type::update('proj_type', [
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
    public function delete($idList)
    {
        try{
            $idList = explode(',', $idList);
            foreach($idList as $id){
                // delete project/record
                proj_type::delete('proj_type', [
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
        if(proj_type::count('proj_type') == 0){
            $typeArray = [
            '大專生國科會計畫',
            '資訊應用服務創新競賽',
            '小專',
            '大專'
            ];
            $id = 1;
            foreach($typeArray as $value){
                proj_type::create('proj_type', [
                    'Id' => $this->newId(),
                    'Name' => $value
                ]);
                
                $id++;
            }
        }
    }

    private function newId()
    {
        $statement = proj_type::prepare("
            select Id from proj_type order by Id desc limit 1;
        ");
        $statement->execute();
        $id = $statement->fetch();
        return (isset($id['Id'])) ? $id['Id'] + 1 : 1;
    }
}