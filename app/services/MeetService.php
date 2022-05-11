<?php

namespace app\services;

use app\core\DbModel;
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
        $statement = meeting::prepare("
        SELECT
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
            INNER JOIN member AS m ON m.Account = meet.Uploader
            LEFT JOIN student AS s ON s.Account = m.Account
            LEFT JOIN teacher AS t ON t.Account = m.Account 
        WHERE
            (meet.Deleted LIKE '' 
            OR isnull( meet.Deleted ))             
            ".            
            ((!empty($search))
            ? 
            "and (meet.Title like '%$search%'
             or meet.Place like '%$search%'
             or meet.Time like '%$search%'
             or s.Name like '%$search%'
             or t.Name like '%$search%'
             or meet.Content like '%$search%')"
             :' ').
            " limit ".(($page-1)*10).", ".($page*10).";"
        );
        $statement->execute();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (!empty($data)) {
            $index = 0;
            foreach ($data as $row) {
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
                    $data[$index]['Tag'] = $tag;
                } else {
                    $data[$index]['Tag'] = [];
                }
                $index++;
            }
        }
        return $data;
    }

    public function getOne($id)
    {
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

        return $data;
    }

    public function add($request, $files = null, $tags = null)
    {
        try {
            if ($this->checkExtensions($files)) {
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
                        $path = str_replace("\\", "\\\\", dirname(dirname(__DIR__)) . "\public\storage\meeting\\" . $fileName);
                        move_uploaded_file($files['tmp_name'][$key], $path); //upload files

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
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return 'success';
    }

    public function update($id, $request, $files = null, $tags = null, $isClearOldList = [])
    {
        try {
            $meeting = meeting::findOne('meeting', [
                'Id' => $id
            ]);
            $member = member::findOne('member', [
                'Account' => $request['USER']
            ]);
            if ($request['USER'] != $meeting['Uploader'] ?? '' || !$member['IsAdmin']) return "unauthorized.";
            if ($this->checkExtensions($files)) {                
                foreach ($isClearOldList as $fileName) {
                    if (empty($fileName)) break;
                    $data = meeting_file::findOne('meeting_file', [
                        'Meet_Id' => $id,
                        'Name' => $fileName
                    ]);

                    if (!empty($data)) {
                        $directory = $data['Url'];
                        unlink($directory);
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
                            unlink($existFile['Url']);
                        }
                        $extension = pathinfo($value, PATHINFO_EXTENSION);
                        $fileName = md5($value . time()) . '.' . $extension;
                        /*
                            temp= explode('.',$file_name);
                            $extension = end($temp);
                        */
                        $path = str_replace("\\", "\\\\", dirname(dirname(__DIR__)) . "\public\storage\meeting\\" . $fileName);
                        move_uploaded_file($files['tmp_name'][$key], $path); //upload files

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
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return 'success';
    }

    public function delete($idList)
    {
        try {
            $idlist = explode(',', $idList);
            foreach ($idlist as $id) {
                meeting::update('meeting', [
                    'Deleted' => date('Y-m-d h:i:s', time())
                ], [
                    'Id' => $id
                ]);
            }
        } catch (Exception $e) {
            return $e->getMessage();
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

    private function newId()
    {
        $statement = meeting::prepare("
            select Id from meeting order by Id desc limit 1;
        ");
        $statement->execute();
        $id = $statement->fetch();
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
            $check_Array[] = pathinfo($value, PATHINFO_EXTENSION);
        }
        $diff = array_diff($check_Array, $allow_extensions);
        return empty($diff);
    }
}
