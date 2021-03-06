<?php

namespace app\services;

use app\core\DbModel;
use app\core\Exception\InternalServerErrorException;
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
        try {
            $data = proj_type::get('proj_type');
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        return $data;
    }

    public function getAll(int $page = 1, $search = null)
    {
        try {
            $search = $this->addSlashes($search);
            $statement = DbModel::prepare("
            select * from proj_type "
                .
                (($search != null) ?
                    " 
            where 
            Name like :search 
            " : ""
                )
            .
            " Order by 
                Id desc 
            "
            .
            " limit " . (($page - 1) * $_ENV['PAGE_ITEM_NUM']) . ", " . ($_ENV['PAGE_ITEM_NUM']) . ";");
            if ($search != null) {
                $statement->bindValue(':search', "%" . $search . "%");
            }
            $statement->execute();
            $data['list'] = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement = null;
            $data['page'] = $this->getAllTypePage($search);
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        return $data;
    }

    public function getAllTypePage($search = null)
    {
        $search = $this->addSlashes($search);
        try{
            $statement =  DbModel::prepare("
            select count(*) from proj_type "
                .
                (($search != null) ?
                    " 
            where 
            Name like :search  
            " : ""
                ));
            if ($search != null) {
                $statement->bindValue(':search', "%" . $search . "%");
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

    public function getProject($id = '%', $page = 1, $search = null)
    {
        $search = $this->addSlashes($search);
        try{
            $statement = project::prepare("
            SELECT DISTINCT
                p.Id,
                p.Name,
                p.Description,
                p.Creater,
                p.CreateTime,
                type.`Name` as Type_name,						
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
                p.Proj_type like :id  and (
                    ISNULL(p.Deleted) or p.Deleted like ''	
                    ) " .
                ((!empty($search))
                    ?
                    "and (p.NAME like :search 
                or p.Description like :search 
                or p.CreateTime like :search 
                or s.Name like :search 
                or t.Name like :search 
                or pt.Name like :search 
                )"
                    : ' ')
                . " 
            ORDER BY
                p.CreateTime DESC "
                .
                " limit " . (($page - 1) * $_ENV['PAGE_ITEM_NUM']) . ", " . ($page * $_ENV['PAGE_ITEM_NUM']) . ";");
            $statement->bindValue(':id', $id);
            if ($search != null) {
                $statement->bindValue(':search', "%" . $search . "%");
            }
            $statement->execute();
            $data['list'] = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($data)) {
                $index = 0;
                foreach ($data['list'] as $row) {                    
                    $tag = dbModel::get('proj_tag', [
                        'Project_Id' => $row['Id']
                    ]);
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
        } catch (Exception) {
            throw new InternalServerErrorException();
        }        
        $statement = null;
        $data['page'] = $this->getProjectListPage($id, $search);
        return $data;
    }

    public function getProjectListPage($id, $search = null)
    {
        $search = $this->addSlashes($search);
        try{
            $statement =  DbModel::prepare("
            SELECT 
                count(DISTINCT p.Id)
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
                    "and (p.NAME like :search 
                or p.Description like :search 
                or p.CreateTime like :search 
                or s.Name like :search 
                or t.Name like :search 
                or pt.Name like :search 
                )"
                    : ' '
                ));
            if ($search != null) {
                $statement->bindValue(':search', "%" . $search . "%");
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
    public function add(string $name)
    {
        $res = proj_type::create('proj_type', [
            'Id' => $this->newId(),
            'Name' => $name,
        ]);        
        return $res ?? false;
    }
    public function update($id, $name)
    {
        $res = proj_type::update('proj_type', [
            'Name' => $name
        ], [
            'Id' => $id
        ]);
        return $res ?? false;
    }
    public function delete($idList)
    {
        $idList = explode(',', $idList);
        foreach ($idList as $id) {
            // delete project/record
            if (project::count('project', ['Proj_type' => $id]) > 0) {
                $type_name = proj_type::findOne('proj_type', ['Id' => $id])['Name'];
                return "???{$type_name}?????????????????????????????????";
            }
        }
        foreach ($idList as $id) {
            proj_type::delete('proj_type', [
                'Id' => $id
            ]);
        }
        return 'success';
    }

    public function addSlashes($string = null)
    {
        return  empty($string) ? $string : addslashes($string);
    }

    private function install()
    {
        if (proj_type::count('proj_type') == 0) {
            $typeArray = [
                '????????????????????????',
                '??????????????????????????????',
                '??????',
                '??????'
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
        try{
            $statement = proj_type::prepare("
                select Id from proj_type order by Id desc limit 1;
            ");
            $statement->execute();
            $id = $statement->fetch();
        } catch (Exception) {
            throw new InternalServerErrorException();
        }        
        $statement = null;
        return (isset($id['Id'])) ? $id['Id'] + 1 : 1;
    }
}
