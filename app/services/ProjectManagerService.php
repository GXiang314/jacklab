<?php

namespace app\services;

use app\core\DbModel;

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

    public function getProject($id)
    {
        $statement = project::prepare("
        SELECT
            p.Id,
            p.NAME,
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
        WHERE
            p.Proj_type = '{$id}' 
        ORDER BY
            p.CreateTime DESC;");
        $statement->execute();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
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
    public function delete($id)
    {
        try{
            // delete student
            proj_type::delete('proj_type', [
                'Id' => $id
            ]);            
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