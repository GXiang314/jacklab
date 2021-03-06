<?php

namespace app\services;

use app\core\DbModel;
use app\core\Exception\InternalServerErrorException;
use app\model\member;
use app\model\proj_file;
use app\model\proj_member;
use app\model\proj_record;
use app\model\proj_tag;
use app\model\project;
use Exception;

class ProjectRecordService
{

    public function getAll($project_Id, $page = 1, $search = null)
    {
        $search = $this->addSlashes($search);
        try{
            $statement = project::prepare("
            SELECT
                p.Id,
                p.Name,
                p.Description,
                p.Creater,
                p.CreateTime,
                pt.Id as Type_id,
            CASE
                    s.`Name` 
                    WHEN s.`Name` THEN
                    s.`Name` ELSE t.NAME 
                END AS Creater_name 
            FROM
                project AS p
                LEFT JOIN student AS s ON s.Account = p.Creater
                LEFT JOIN teacher AS t ON t.Account = p.Creater
                LEFT JOIN proj_type AS pt ON pt.Id = p.Proj_type
            WHERE
                p.Id = '{$project_Id}' and (
                ISNULL(p.Deleted) or p.Deleted like ''	
                )
                ;");
            $statement->execute();
            $data = $statement->fetch(\PDO::FETCH_ASSOC);
            $statement = null;
            if (empty($data)) return "";

            $statement = project::prepare("
            SELECT
            CASE
                    s.`Name` 
                    WHEN s.`Name` THEN
                    s.`Name` ELSE t.NAME 
                END AS Name,
                m.Account,
                CASE
                    s.`Image` 
                    WHEN s.`Image` THEN
                    s.`Image` ELSE t.Image 
                END AS Image
            FROM
                member AS m
                INNER JOIN proj_member AS pm ON pm.Account = m.Account
                INNER JOIN project AS p ON p.Id = pm.Project_Id
                LEFT JOIN student AS s ON s.Account = m.Account
                LEFT JOIN teacher AS t ON t.Account = m.Account 
            WHERE
                p.id = '{$project_Id}';");
            $statement->execute();
            $member = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement = null;
            $data['Member'] = $member;

            $statement = project::prepare("
            SELECT
            pt.`Name`
            FROM
                proj_tag AS pt
                INNER JOIN project AS p ON p.Id = pt.Project_Id
            WHERE
                p.id = '{$project_Id}';");
            $statement->execute();
            $tag = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement = null;
            $data['Tag'] = $tag ?? [];

            $statement = proj_record::prepare("
            SELECT
                pr.Id AS Id,
                pr.Remark AS Remark,
                pr.CreateTime AS CreateTime,
                pr.Uploader,
            CASE
                    s.`Name` 
                    WHEN s.`Name` THEN
                    s.`Name` ELSE t.NAME 
                END AS Uploader_name,
                p.`Name` AS Project_name 
            FROM
                proj_record AS pr
                INNER JOIN project AS p ON p.Id = pr.Project_Id
                LEFT JOIN member AS m ON m.Account = pr.Uploader
                LEFT JOIN student AS s ON s.Account = m.Account
                LEFT JOIN teacher AS t ON t.Account = m.Account 
            WHERE
                pr.Project_Id = '{$project_Id}' 
                AND (
                pr.Deleted LIKE '' 
                OR isnull( pr.Deleted )) "
                .
                ((!empty($search))
                    ?
                    "and (pr.Remark like :search 
                or pr.CreateTime like :search 
                or s.Name like :search 
                or t.Name like :search )"
                    : ' ')
                . " 
            ORDER BY 
                pr.CreateTime desc
            "
                . " limit " . (($page - 1) * $_ENV['PAGE_ITEM_NUM']) . ", " . ($page * $_ENV['PAGE_ITEM_NUM']) .
                ";");
            if ($search != null) {
                $statement->bindValue(':search', "%" . $search . "%");
            }
            $statement->execute();
            $data['Record'] = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement = null;
            $data['page'] = $this->getRecordListPage($project_Id, $search);
            if (!empty($data['Record'])) {
                $index = 0;
                foreach ($data['Record'] as $row) {
                    $statement = proj_file::prepare("
                    SELECT
                        pf.Id,
                        pf.Name,
                        pf.Size 
                    FROM
                        proj_file AS pf 
                    WHERE
                        pf.Proj_record = '{$row['Id']}';");
                    $statement->execute();
                    $file = $statement->fetch(\PDO::FETCH_ASSOC);
                    if (!empty($file)) {
                        $data['Record'][$index]['File'] = $file;
                    } else {
                        $data['Record'][$index]['File'] = [];
                    }
                    $index++;
                }
            }
        } catch (Exception) {
            throw new InternalServerErrorException();
        }

        
        $statement = null;
        return $data;
    }

    public function getOne($project_Id, $page = 1, $search = null)
    {
        $search = $this->addSlashes($search);
        try{
            $statement = proj_record::prepare("
            SELECT
                pr.Id AS Id,
                pr.Remark AS Remark,
                pr.CreateTime AS CreateTime,
                pr.Uploader,
            CASE
                    s.`Name` 
                    WHEN s.`Name` THEN
                    s.`Name` ELSE t.NAME 
                END AS Uploader_name,
                p.`Name` AS Project_name 
            FROM
                proj_record AS pr
                INNER JOIN project AS p ON p.Id = pr.Project_Id
                LEFT JOIN member AS m ON m.Account = pr.Uploader
                LEFT JOIN student AS s ON s.Account = m.Account
                LEFT JOIN teacher AS t ON t.Account = m.Account 
            WHERE
                pr.Project_Id = '{$project_Id}' 
                AND (
                pr.Deleted LIKE '' 
                OR isnull( pr.Deleted )) "
                .
                ((!empty($search))
                    ?
                    "and (pr.Remark like :search 
                or pr.CreateTime like :search 
                or s.Name like :search 
                or t.Name like :search )"
                    : ' ')
                . " 
            ORDER BY 
                pr.CreateTime desc
            "
                . " limit " . (($page - 1) * $_ENV['PAGE_ITEM_NUM']) . ", " . ($_ENV['PAGE_ITEM_NUM']) . ";");
            if ($search != null) {
                $statement->bindValue(':search', "%" . $search . "%");
            }
            $statement->execute();
            $data['list'] = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $statement = null;
            $data['page'] = $this->getRecordListPage($project_Id, $search);
            if (!empty($data['list'])) {
                $index = 0;
                foreach ($data['list'] as $row) {
                    $statement = proj_file::prepare("
                    SELECT
                        pf.Id,
                        pf.Name,
                        pf.Size 
                    FROM
                        proj_file AS pf 
                    WHERE
                        pf.Proj_record = '{$row['Id']}';");
                    $statement->execute();
                    $file = $statement->fetch(\PDO::FETCH_ASSOC);
                    $statement = null;
                    if (!empty($file)) {
                        $data['list'][$index]['File'] = $file;
                    } else {
                        $data['list'][$index]['File'] = [];
                    }
                    $index++;
                }
            }
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        $statement = null;
        return $data;
    }

    public function getRecordListPage($id, $search = null)
    {
        $search = $this->addSlashes($search);
        try{
            $statement =  DbModel::prepare("
            SELECT
                count(*) 
            FROM
                proj_record AS pr
                INNER JOIN project AS p ON p.Id = pr.Project_Id
                LEFT JOIN member AS m ON m.Account = pr.Uploader
                LEFT JOIN student AS s ON s.Account = m.Account
                LEFT JOIN teacher AS t ON t.Account = m.Account 
            WHERE
                pr.Project_Id = '{$id}' 
                AND (
                pr.Deleted LIKE '' 
                OR isnull( pr.Deleted )) "
                .
                ((!empty($search))
                    ?
                    "and (pr.Remark like :search 
                or pr.CreateTime like :search 
                or s.Name like :search 
                or t.Name like :search )"
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
        $statement = null;
        $page = ceil((float)$count / $_ENV['PAGE_ITEM_NUM']);
        return $page == 0 ? 1 : $page;
    }

    public function tagUploadValidate($addArray = [])
    {
        $addCount = count($addArray);
        return $addCount > intval($_ENV['MAX_TAG_NUM']);
    }

    public function getProjectTag($search = null)
    {
        try{
            $statement = DbModel::prepare("
            SELECT DISTINCT Name 
            FROM
                proj_tag " .
                ((!empty($search)) ?
                    "Where 
                Name like :search 
            " : "") .
                ";");
            if (!empty($search)) {
                $statement->bindValue(':search', "%" . $search . "%");
            }
            $statement->execute();
            $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception) {
            throw new InternalServerErrorException();
        }        
        $statement = null;
        return $data;
    }

    public function create($request, $tags = null)
    {
        try {
            if ($this->tagUploadValidate($tags ?? [])) return "????????????????????????";
            $now = date("Y-m-d H:i:s", time());
            project::create('project', [
                'Name' => $request['Name'],
                'Description' => $request['Description'],
                'CreateTime' => $now,
                'Proj_type' => $request['Proj_type'],
                'Creater' => $request['USER'],
            ]);
            $id = project::findOne('project', [
                'CreateTime' => $now,
                'Creater' => $request['USER'],
            ])['Id'] ?? '';
            foreach ($request['Member'] as $member) {
                proj_member::create('proj_member', [
                    'Project_Id' => $id,
                    'Account' => $member
                ]);
            }
            if ($tags != null) {
                foreach ($tags as $tag) {
                    proj_tag::create('proj_tag', [
                        'Name' => $tag,
                        'Project_Id' => $id
                    ]);
                }
            }
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        return 'success';
    }

    public function addRecord($request, $file = null)
    {
        try {
            if ($this->checkExtensions($file)) {
                $id = $this->newId();
                proj_record::create('proj_record', [
                    'Id' => $id,
                    'Remark' => $request['Remark'],
                    'CreateTime' => date("Y-m-d H:i:s"),
                    'Project_Id' => $request['Project_Id'],
                    'Uploader' => $request['USER'],
                ]);
                if (!empty(DbModel::findOne('proj_record', ['Id' => $id]))) {
                    if ($file != null) {
                        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $fileName = md5($file['name'] . time()) . '.' . $extension;
                        /*
                            temp= explode('.',$file_name);
                            $extension = end($temp);
                        */
                        $path = "\storage\project\\" . $fileName;
                        $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
                        move_uploaded_file($file['tmp_name'], dirname(dirname(__DIR__)) .  DIRECTORY_SEPARATOR. "public" . $path); //upload files

                        proj_file::create('proj_file', [
                            'Name' => $file['name'],
                            'Type' => $extension,
                            'Size' => $file['size'],
                            'Url' => $path,
                            'Proj_record' => $id
                        ]);
                    }
                }
            } else {
                return '????????????????????????';
            }
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        return 'success';
    }

    public function update($id, $request, $tags = null)
    {
        try {
            if ($this->tagUploadValidate($tags ?? [])) return "????????????????????????";
            if ($this->isDeleteCreater($id, $request['Member'])) return "?????????????????????";
            project::update('project', [
                'Name' => $request['Name'],
                'Description' => $request['Description'],
                'Proj_type' => $request['Proj_type'],
            ], [
                'Id' => $id
            ]);
            proj_member::delete('proj_member', [
                'Project_Id' => $id
            ]);
            proj_tag::delete('proj_tag', [
                'Project_Id' => $id
            ]);

            foreach ($request['Member'] as $member) {
                proj_member::create('proj_member', [
                    'Project_Id' => $id,
                    'Account' => $member
                ]);
            }

            if ($tags != null) {
                foreach ($tags as $tag) {
                    proj_tag::create('proj_tag', [
                        'Name' => $tag,
                        'Project_Id' => $id
                    ]);
                }
            }
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        return 'success';
    }

    public function updateRecord($id, $request, $file = null)
    {
        try {
            if ($this->checkExtensions($file)) {
                proj_record::update('proj_record', [
                    'Remark' => $request['Remark'],
                    'Uploader' => $request['USER'],
                ], [
                    'Id' => $id
                ]);
                if ($file != null) {
                    $data = proj_file::findOne('proj_file', [
                        'Proj_record' => $id
                    ]);
                    if (!empty($data)) {
                        unlink(str_replace("\\", DIRECTORY_SEPARATOR, $data['Url']));
                        proj_file::delete('proj_file', [
                            'Proj_record' => $id
                        ]);
                    }
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $fileName = md5($file['name'] . time()) . '.' . $extension;
                    /*
                        temp= explode('.',$file_name);
                        $extension = end($temp);
                    */
                    $path = "\storage\project\\" . $fileName;
                    $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
                    move_uploaded_file($file['tmp_name'], dirname(dirname(__DIR__)) .  DIRECTORY_SEPARATOR. "public" . $path); //upload files

                    proj_file::create('proj_file', [
                        'Name' => $file['name'],
                        'Type' => $extension,
                        'Size' => $file['size'],
                        'Url' => $path,
                        'Proj_record' => $id
                    ]);
                }
            } else {
                return '????????????????????????';
            }
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        return 'success';
    }

    public function delete($idList)
    {
        try {
            $idlist = explode(',', $idList);
            foreach ($idlist as $id) {
                project::update('project', [
                    'Deleted' => date('Y-m-d H:i:s', time())
                ], [
                    'Id' => $id
                ]);
            }
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        return 'success';
    }

    public function deleteRecord($idList)
    {
        try {
            $idlist = explode(',', $idList);
            foreach ($idlist as $id) {
                proj_record::update('proj_record', [
                    'Deleted' => date('Y-m-d H:i:s', time())
                ], [
                    'Id' => $id
                ]);
            }
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        return 'success';
    }

    private function isDeleteCreater($proj_id, $member)
    {
        $data = project::findOne('project', [
            'Id' => $proj_id
        ]);
        if($data){
            return !in_array($data['Creater'], $member);
        }
        return false;
    }

    // public function forceDelete($idList)
    // {
    //     try {            
    //         $idlist = explode(',', $idList);
    //         foreach ($idlist as $id) {
    //             $files = meeting_file::where('Meet_Id', $id)->get();
    //             foreach ($files as $file) {
    //                 if ($file == []) break;
    //                 $directory = $file['Url'];
    //                 Storage::delete($directory);
    //             }
    //             meeting_file::where('Meet_Id', $id)->delete();
    //             meeting_tag::where('Meet_Id', $id)->delete();
    //             meeting_member::where('Meet_Id', $id)->delete();
    //             meeting::where('Id', $id)->delete();
    //         }
    //     } catch (Exception $e) {
    //         return $e->getMessage();
    //     }
    //     return 'success';
    // }

    public function addSlashes($string = null)
    {
        return  empty($string) ? $string : addslashes($string);
    }

    private function newId()
    {
        try{
        $statement = proj_record::prepare("
            select Id from proj_record order by Id desc limit 1;
        ");
        $statement->execute();
        $id = $statement->fetch();
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        $statement = null;
        return (isset($id['Id'])) ? $id['Id'] + 1 : 1;
    }

    public function getFile($id)
    {
        $file = proj_file::findOne('proj_file', [
            'Id' => $id
        ]);
        return $file;
    }

    public function checkExtensions($file = null)
    {
        if ($file == null) return true;
        $allow_extensions = explode(',', $_ENV['ALLOW_EXTENSIONS']);
        $check_Array = [];
        $check_Array[] = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $diff = array_diff($check_Array, $allow_extensions);
        return empty($diff);
    }
}
