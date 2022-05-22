<?php

namespace app\services;

use app\core\DbModel;
use app\model\proj_record;
use app\model\proj_tag;
use app\model\proj_type;
use app\model\project;
use Exception;

class ProjectManagerService
{

    public function __construct()
    {
        $this->install();
    }


    public function getAllNoPaging()
    {
        try{
            $data = proj_type::get('proj_type');
        }catch(Exception $e){
            return $e->getMessage();
        }
        return $data;
    }

    public function getAll(int $page = 1, $search = null)
    {
        try {
            $statement = DbModel::prepare("
            select * from proj_type "
                .
                (($search != null) ?
                    " 
            where 
            Name like '%$search%'
            " : ""
                )
                .
                " limit " . (($page - 1) * $_ENV['PAGE_ITEM_NUM']) . ", " . ($_ENV['PAGE_ITEM_NUM']) . ";");
            // $statement->bindValue(":search", "%$search%");
            $statement->execute();
            $data['list'] = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $data['page'] = $this->getAllTypePage($search);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $data;
    }

    public function getAllTypePage($search = null)
    {
        $statement =  DbModel::prepare("
        select count(*) from proj_type "
        .
        (($search != null) ?
            " 
        where 
        Name like '%$search%' 
        " : ""
        ));
        $statement->execute();
        $count = $statement->fetchColumn();
        $page = ceil((float)$count / $_ENV['PAGE_ITEM_NUM']);
        return $page == 0 ? 1 : $page;
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
            Type.`Name` as Type_name,						
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
            LEFT JOIN proj_type AS type ON type.Id = p.Proj_type
        WHERE
            p.Proj_type like '{$id}'  and (
                ISNULL(p.Deleted) or p.Deleted like ''	
                ) " .
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
                : ' ')
            . " 
        ORDER BY
            p.CreateTime DESC "
            .
            " limit " . (($page - 1) * $_ENV['PAGE_ITEM_NUM']) . ", " . ($page * $_ENV['PAGE_ITEM_NUM']) . ";");
        $statement->execute();
        $data['list'] = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (!empty($data)) {
            $index = 0;
            foreach ($data['list'] as $row) {
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
                    $data['list'][$index]['Tag'] = $tag;
                } else {
                    $data['list'][$index]['Tag'] = [];
                }
                $statement = proj_record::prepare("
                SELECT
                    count(*) AS c 
                FROM
                    proj_record AS pr 
                WHERE
                    pr.Project_Id = {$row['Id']} 
                    AND (
                        ISNULL( pr.Deleted ) 
                    OR pr.Deleted LIKE '')
                ");
                $statement->execute();
                $data['list'][$index]['Record_count'] = $statement->fetch(\PDO::FETCH_COLUMN);
                $index++;
            }
        }
        $data['page'] = $this->getProjectListPage($id, $search);
        return $data;
    }

    public function getProjectListPage($id, $search = null)
    {
        $statement =  DbModel::prepare("
        SELECT DISTINCT
            count(*)
        FROM
            project AS p
            LEFT JOIN student AS s ON s.Account = p.Creater
            LEFT JOIN teacher AS t ON t.Account = p.Creater 
            LEFT JOIN proj_tag AS pt ON pt.Project_Id = p.Id 
            LEFT JOIN proj_type AS type ON type.Id = p.Proj_type
        WHERE
            p.Proj_type like '{$id}'  and (
                ISNULL(p.Deleted) or p.Deleted like ''	
                ) " .
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
                : ' '
            ));
        $statement->execute();
        $count = $statement->fetchColumn();
        $page = ceil((float)$count / $_ENV['PAGE_ITEM_NUM']);
        return $page == 0 ? 1 : $page;
    }
    public function add(string $name)
    {
        try {
            proj_type::create('proj_type', [
                'Id' => $this->newId(),
                'Name' => $name,
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return 'success';
    }
    public function update($id, $name)
    {
        try {
            proj_type::update('proj_type', [
                'Name' => $name
            ], [
                'Id' => $id
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return 'success';
    }
    public function delete($idList)
    {
        try {
            $idList = explode(',', $idList);
            foreach ($idList as $id) {
                // delete project/record
                if (project::count('project', ['Proj_type' => $id]) > 0) {
                    $type_name = proj_type::findOne('proj_type', ['Id' => $id])['Name'];
                    return "「{$type_name}」已包含專案，無法刪除";
                }
            }
            foreach ($idList as $id) {
                proj_type::delete('proj_type', [
                    'Id' => $id
                ]);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return 'success';
    }

    private function install()
    {
        if (proj_type::count('proj_type') == 0) {
            $typeArray = [
                '大專生國科會計畫',
                '資訊應用服務創新競賽',
                '小專',
                '大專'
            ];
            $id = 1;
            foreach ($typeArray as $value) {
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
