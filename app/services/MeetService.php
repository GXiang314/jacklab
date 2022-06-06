<?php

namespace app\services;

use app\core\DbModel;
use app\core\Exception\InternalServerErrorException;
use app\model\meeting;
use app\model\meeting_file;
use app\model\meeting_member;
use app\model\meeting_tag;
use app\model\member;
use Exception;

class MeetService
{

    public function getAll($page = 1, $search = null)
    {
        try{
            $statement = meeting::prepare("
            SELECT DISTINCT
                meet.Id,
                meet.Title,
                meet.Place,
                meet.Time,
                meet.Uploader,
            CASE
                    s.`Name` 
                    WHEN s.`Name` THEN
                    s.`Name` ELSE t.NAME 
            END AS Name 
            FROM
                meeting AS meet
                LEFT JOIN member AS m ON m.Account = meet.Uploader
                LEFT JOIN student AS s ON s.Account = m.Account
                LEFT JOIN teacher AS t ON t.Account = m.Account 
                LEFT JOIN meeting_tag AS mt ON mt.Meet_Id = meet.Id 
            WHERE
                (meet.Deleted LIKE '' 
                OR isnull( meet.Deleted ))             
                " .
                    ((!empty($search))
                        ?
                        "and (meet.Title like :search 
                or meet.Place like :search 
                or meet.Time like :search 
                or s.Name like :search 
                or t.Name like :search 
                or meet.Content like :search 
                or mt.Name like :search 
                )"
                : ' ') 
            ." 
            ORDER BY 
                meet.Time desc
            "
            .
            " limit " . (($page - 1) * $_ENV['PAGE_ITEM_NUM']) . ", " . ($_ENV['PAGE_ITEM_NUM']) . ";"
            );
            if ($search != null) {
                $statement->bindValue(':search', "%" . $search . "%");
            }
            $statement->execute();
            $data['list'] = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $data['page'] = $this->getAllMeetPage($search);
            if (!empty($data['list'])) {
                $index = 0;
                foreach ($data['list'] as $row) {
                    $statement = meeting::prepare("
                    SELECT
                        * 
                    FROM
                        meeting_tag AS mt 
                    WHERE
                        mt.Meet_Id = '{$row['Id']}';");
                    $statement->execute();
                    $tag = $statement->fetchAll(\PDO::FETCH_ASSOC);
                    if (!empty($tag)) {
                        $data['list'][$index]['Tag'] = $tag;
                    } else {
                        $data['list'][$index]['Tag'] = [];
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

    public function getOne($id)
    {
        try{
            $statement = meeting::prepare("
            SELECT
                meet.*,
                CASE s.`Name` WHEN s.`Name` THEN s.`Name` ELSE t.NAME END as Name 
            FROM
                meeting AS meet
                LEFT JOIN student AS s ON s.Account = meet.Uploader
                LEFT JOIN teacher AS t ON t.Account = meet.Uploader 
            WHERE
                meet.Id = '{$id}' and
                (meet.Deleted LIKE '' 
                OR isnull( meet.Deleted ));");
            $statement->execute();
            $data = $statement->fetch(\PDO::FETCH_ASSOC);
            if (!empty($data)) {
                $statement = meeting::prepare("
                SELECT
                    * 
                FROM
                    meeting_tag AS mt 
                WHERE
                    mt.Meet_Id = '{$id}';");
                $statement->execute();
                $tag = $statement->fetchAll(\PDO::FETCH_ASSOC);
                if (!empty($tag)) {
                    $data['Tag'] = $tag;
                } else {
                    $data['Tag'] = [];
                }
                $statement = meeting::prepare("
                SELECT
                    CASE s.`Name` WHEN s.`Name` THEN s.`Name` ELSE t.NAME END as Name,
                    m.Account 
                FROM
                    meeting_member AS mm
                    INNER JOIN member AS m ON m.Account = mm.Account
                    LEFT JOIN student AS s ON s.Account = m.Account
                    LEFT JOIN teacher AS t ON t.Account = m.Account 
                WHERE
                    mm.Meet_Id = '{$id}';");
                $statement->execute();
                $member = $statement->fetchAll(\PDO::FETCH_ASSOC);
                if (!empty($member)) {
                    $data['Member'] = $member;
                } else {
                    $data['Member'] = [];
                }

                $statement = meeting::prepare("
                SELECT
                    mf.Id, mf.Name, mf.Size
                FROM
                    meeting_file as mf
                WHERE
                    mf.Meet_Id = '{$id}';");
                $statement->execute();
                $file = $statement->fetchAll(\PDO::FETCH_ASSOC);
                if (!empty($file)) {
                    $data['File'] = $file;
                } else {
                    $data['File'] = [];
                }
            }
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        $statement = null;
        return $data;
    }

    public function getAllMeetPage($search = null)
    {
        try{
            $statement =  DbModel::prepare("
            select count(DISTINCT meet.Id) from 
            meeting AS meet
                LEFT JOIN member AS m ON m.Account = meet.Uploader
                LEFT JOIN student AS s ON s.Account = m.Account
                LEFT JOIN teacher AS t ON t.Account = m.Account 
                LEFT JOIN meeting_tag AS mt ON mt.Meet_Id = meet.Id 
            Where 
            (meet.Deleted LIKE '' 
                OR isnull( meet.Deleted )) 
            "
                .
                (($search != null) ?
                    " 
            and (
                meet.Title like :search 
                or meet.Place like :search 
                or meet.Time like :search 
                or s.Name like :search 
                or t.Name like :search 
                or meet.Content like :search 
                or mt.Name like :search )
            " : "".";"
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

    public function add($request, $files = null, $tags = null)
    {
        try {
            if ($this->checkExtensions($files)) {
                if ($this->fileUploadValidate($files['Name'] ?? [])) return "最多上傳五個檔案";
                if ($this->tagUploadValidate($tags ?? [])) return "最多上傳五個標籤";
                $id = $this->newId();
                meeting::create('meeting', [
                    'Id' => $id,
                    'Title' => $request['Title'],
                    'Content' => $request['Content'],
                    'Time' => $request['Time'],
                    'Place' => $request['Place'],
                    'Uploader' => $request['USER'],
                ]);
                foreach ($request['Member'] as $member) {
                    meeting_member::create('meeting_member', [
                        'Meet_Id' => $id,
                        'Account' => $member
                    ]);
                }
                if ($files != null) {
                    foreach ($files['name'] as $key => $value) {
                        $extension = pathinfo($value, PATHINFO_EXTENSION);
                        $fileName = md5($value . time()) . '.' . $extension;
                        /*
                            temp= explode('.',$file_name);
                            $extension = end($temp);
                        */
                        $path = "\storage\meeting\\" . $fileName;
                        $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
                        move_uploaded_file($files['tmp_name'][$key], dirname(dirname(__DIR__)) .  DIRECTORY_SEPARATOR. "public" . $path); //upload files

                        meeting_file::create('meeting_file', [
                            'Name' => $value,
                            'Type' => $extension,
                            'Size' => $files['size'][$key],
                            'Url' => $path,
                            'Meet_Id' => $id
                        ]);
                    }
                }
                if ($tags != null) {
                    foreach ($tags as $tag) {
                        meeting_tag::create('meeting_tag', [
                            'Name' => $tag,
                            'Meet_Id' => $id
                        ]);
                    }
                }
            } else {
                return '不支援該檔案格式';
            }
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        return 'success';
    }

    public function getMeetingTag($search = null)
    {
        try{
            $statement = DbModel::prepare("
            SELECT DISTINCT Name 
            FROM
                meeting_tag ".
            ((!empty($search)) ? 
            "Where 
                Name like :search 
            ":"").
            ";");
            if(!empty($search)){
                $statement->bindValue(':search', "%".$search."%");
            }
            $statement->execute();        
            $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception) {
            throw new InternalServerErrorException();
        }        
        $statement = null;
        return $data;
    }

    public function fileUploadValidate($addArray = [], $nowArray = [], $clearOldArray = [])
    {
        $addCount = count($addArray);
        $nowCount = count($nowArray);
        $clearOldCount = count($clearOldArray);
        return ($addCount + $nowCount - $clearOldCount) > intval($_ENV['MAX_UPLOAD_NUM']);
    }

    public function tagUploadValidate($addArray = [])
    {
        $addCount = count($addArray);
        return $addCount > intval($_ENV['MAX_TAG_NUM']);
    }

    public function update($id, $request, $files = null, $tags = null, $isClearOldList = [])
    {
        try {
            if ($this->isDeleteCreater($id, $request['Member'])) return "不可刪除上傳者";
            $meeting = meeting::findOne('meeting', [
                'Id' => $id
            ]);
            if ($request['USER'] != $meeting['Uploader'] && !$request['ADMIN']) return "unauthorized.";

            $nowData = meeting_file::get('meeting_file', [
                'Meet_Id' => $id
            ]);
            if ($this->fileUploadValidate($files['name'] ?? [], $nowData, $isClearOldList)) return "最多上傳五個檔案";
            if ($this->tagUploadValidate($tags ?? [])) return "最多上傳五個標籤";
            if ($this->checkExtensions($files)) {
                foreach ($isClearOldList as $fileName) {
                    if (empty($fileName)) break;

                    $data = meeting_file::findOne('meeting_file', [
                        'Meet_Id' => $id,
                        'Name' => $fileName
                    ]);

                    if (!empty($data)) {
                        $directory = $data['Url'];
                        unlink(str_replace("\\", DIRECTORY_SEPARATOR, dirname(dirname(__DIR__)) .  DIRECTORY_SEPARATOR. "public" . $directory));
                        meeting_file::delete('meeting_file', [
                            'Meet_Id' => $id,
                            'Name' => $fileName
                        ]);
                    }
                }
                meeting_tag::delete('meeting_tag', [
                    'Meet_Id' => $id
                ]);
                meeting_member::delete('meeting_member', [
                    'Meet_Id' => $id
                ]);

                meeting::update('meeting', [
                    'Title' => $request['Title'],
                    'Content' => $request['Content'],
                    'Time' => $request['Time'],
                    'Place' => $request['Place'],
                ], [
                    'Id' => $id
                ]);
                foreach ($request['Member'] as $member) {
                    meeting_member::create('meeting_member', [
                        'Meet_Id' => $id,
                        'Account' => $member
                    ]);
                }
                if ($files != null) {
                    foreach ($files['name'] as $key => $value) {
                        $existFile = meeting_file::findOne('meeting_file', [
                            'Name' => $value,
                            'Meet_Id' => $id,
                        ]);
                        if (!empty($existFile)) {
                            meeting_file::delete('meeting_file', [
                                'Id' => $existFile['Id']
                            ]);
                            unlink(str_replace("\\", DIRECTORY_SEPARATOR, dirname(dirname(__DIR__)) .  DIRECTORY_SEPARATOR. "public" . $existFile['Url']));
                        }
                        $extension = pathinfo($value, PATHINFO_EXTENSION);
                        $fileName = md5($value . time()) . '.' . $extension;
                        /*
                            temp= explode('.',$file_name);
                            $extension = end($temp);
                        */
                        $path = "\storage\meeting\\" . $fileName;
                        $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
                        move_uploaded_file($files['tmp_name'][$key], dirname(dirname(__DIR__)) .  DIRECTORY_SEPARATOR. "public" . $path); //upload files

                        meeting_file::create('meeting_file', [
                            'Name' => $value,
                            'Type' => $extension,
                            'Size' => $files['size'][$key],
                            'Url' => $path,
                            'Meet_Id' => $id
                        ]);
                    }
                }
                if ($tags != null) {
                    foreach ($tags as $tag) {
                        meeting_tag::create('meeting_tag', [
                            'Name' => $tag,
                            'Meet_Id' => $id
                        ]);
                    }
                }
            } else {
                return '不支援該檔案格式';
            }
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        return 'success';
    }

    public function delete($idList)
    {
        $idlist = explode(',', $idList);
        foreach ($idlist as $id) {
            meeting::update('meeting', [
                'Deleted' => date('Y-m-d H:i:s', time())
            ], [
                'Id' => $id
            ]);
        }
        return 'success';
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
    private function isDeleteCreater($proj_id, $member)
    {
        $data = meeting::findOne('meeting', [
            'Id' => $proj_id
        ]);
        if($data){
            return !in_array($data['Uploader'], $member);
        }
        return false;
    }

    private function newId()
    {
        try{
            $statement = meeting::prepare("
                select Id from meeting order by Id desc limit 1;
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
        $file = meeting_file::findOne('meeting_file', [
            'Id' => $id
        ]);
        return $file;
    }

    public function checkExtensions($file = null)
    {
        if ($file == null) return true;
        $allow_extensions = explode(',', $_ENV['ALLOW_EXTENSIONS']);
        $check_Array = [];
        foreach ($file['name'] as $key => $value) {
            $check_Array[] = strtolower(pathinfo($value, PATHINFO_EXTENSION));
        }
        $diff = array_diff($check_Array, $allow_extensions);
        return empty($diff);
    }
}
