<?php

namespace app\services;

use app\core\DbModel;
use app\model\game_file;
use app\model\game_member;
use app\model\game_record;
use app\model\game_type;
use app\model\member;
use Exception;

class GameRecordService
{

    public function getAll()
    {
        $statement = game_record::prepare("
        SELECT
            gr.Id AS Id,
            gr.NAME AS G_name,
            gr.Ranking AS G_ranking,
            gr.Game_group AS G_group,
            gr.Game_time AS G_time,
            gr.Uploader,
        CASE
                s.`Name` 
                WHEN s.`Name` THEN
                s.`Name` ELSE t.NAME 
            END AS Uploader_name,
            gt.NAME AS Type_name 
        FROM
            game_record AS gr
            INNER JOIN game_type AS gt ON gt.Id = gr.Game_type
            INNER JOIN member AS m ON m.Account = gr.Uploader
            LEFT JOIN student AS s ON s.Account = m.Account
            LEFT JOIN teacher AS t ON t.Account = m.Account 
        WHERE
            (
            gr.Deleted LIKE '' 
            OR isnull( gr.Deleted ));");
        $statement->execute();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (!empty($data)) {
            $index = 0;
            foreach ($data as $row) {
                $statement = game_member::prepare("
                SELECT
                    s.Id,
                    s.Name	
                FROM
                    game_member AS gm,
                    student as s
                WHERE
                    gm.Game_record = '{$row['Id']}' and
                    gm.Student_Id = s.Id;");
                $statement->execute();
                $member = $statement->fetchAll(\PDO::FETCH_ASSOC);
                if (!empty($member)) {
                    $data[$index]['Member'] = $member;
                } else {
                    $data[$index]['Member'] = [];
                }
                $index++;
            }
        }
        $statement = null;
        return $data;
    }

    public function getOne($id)
    {
        $statement = game_record::prepare("
        SELECT
            gr.Id AS Id,
            gr.NAME AS G_name,
            gr.Ranking AS G_ranking,
            gr.Game_group AS G_group,
            gr.Game_time AS G_time,
            gr.Uploader,
        CASE
                s.`Name` 
                WHEN s.`Name` THEN
                s.`Name` ELSE t.NAME 
            END AS Uploader_name,
            gt.NAME AS Type_name 
        FROM
            game_record AS gr
            INNER JOIN game_type AS gt ON gt.Id = gr.Game_type
            INNER JOIN member AS m ON m.Account = gr.Uploader
            LEFT JOIN student AS s ON s.Account = m.Account
            LEFT JOIN teacher AS t ON t.Account = m.Account 
        WHERE
            (
            gr.Deleted LIKE '' 
            OR isnull( gr.Deleted ));");
        $statement->execute();
        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!empty($data)) {
            $statement = game_member::prepare("
            SELECT
                s.Id,
                s.Name	
            FROM
                game_member AS gm,
                student as s
            WHERE
                gm.Game_record = '{$id}' and
                gm.Student_Id = s.Id;");
            $statement->execute();
            $member = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($member)) {
                $date['Member'] = $member;
            } else {
                $data['Member'] = [];
            }

            $statement = game_file::prepare("
            SELECT
                gf.Id,
                gf.Name,
                gf.Size 
            FROM
                game_file AS gf 
            WHERE
                gf.Game_record = '{$id}';");
            $statement->execute();
            $file = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($file)) {
                $data['File'] = $file;
            } else {
                $data['File'] = [];
            }
        }
        $statement = null;
        return $data;
    }

    public function add($request, $files = null)
    {
        try {
            if ($this->checkExtensions($files)) {
                $id = $this->newId();
                game_record::create('game_record', [
                    'Id' => $id,
                    'Name' => $request['Name'],
                    'Game_group' => $request['Game_group'],
                    'Ranking' => $request['Ranking'],
                    'Game_time' => $request['Game_time'],
                    'Uploader' => $request['USER'],
                    'Game_type' => $request['Game_type'],
                ]);
                foreach ($request['Member'] as $member) {
                    game_member::create('game_member', [
                        'Game_record' => $id,
                        'Student_Id' => $member
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
                        $path = "\storage\game\\" . $fileName;
                        move_uploaded_file($files['tmp_name'][$key], dirname(dirname(__DIR__)) . "\public".$path); //upload files

                        game_file::create('game_file', [
                            'Name' => $value,
                            'Type' => $extension,
                            'Size' => $files['size'][$key],
                            'Url' => $path,
                            'Game_record' => $id
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

    public function update($id, $request, $files = null, $isClearOldList = [])
    {
        try {
            if ($this->checkExtensions($files)) {
                $game_record = game_record::findOne('game_record', [
                    'Id' => $id
                ]);
                $member = member::findOne('member', [
                    'Account' => $request->USER
                ]);
                if ($request->USER == $game_record['Uploader'] ?? '' || $member['IsAdmin']) {
                    foreach ($isClearOldList as $fileName) {
                        if (empty($fileName)) break;
                        $data = game_file::findOne('game_file', [
                            'Game_record' => $id,
                            'Name' => $fileName
                        ]);
                        if (!empty($data)) {
                            $directory = $data['Url'];
                            unlink($directory);
                            game_file::delete('game_file', [
                                'Game_record' => $id,
                                'Name' => $fileName
                            ]);
                        }
                    }
                    game_member::delete('game_member', [
                        'Game_record' => $id
                    ]);

                    game_record::update('game_record', [
                        'Name' => $request['Name'],
                        'Game_group' => $request['Game_group'],
                        'Ranking' => $request['Ranking'],
                        'Game_time' => $request['Game_time'],
                        'Game_type' => $request['Game_type'],
                        'Uploader' => $request['USER'],
                    ], [
                        'Id' => $id
                    ]);
                    foreach ($request['Member'] as $member) {
                        game_member::create('game_member', [
                            'Game_record' => $id,
                            'Student_Id' => $member
                        ]);
                    }
                    if ($files != null) {
                        foreach ($files['name'] as $key => $value) {
                            $existFile = game_file::findOne('game_file', [
                                'Name' => $value,
                                'Game_record' => $id,
                            ]);
                            if (!empty($existFile)) {
                                game_file::delete('game_file', [
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
                            $path = "\storage\game\\" . $fileName;
                            move_uploaded_file($files['tmp_name'][$key], dirname(dirname(__DIR__)) . "\public" . $path); //upload files

                            game_file::create('game_file', [
                                'Name' => $value,
                                'Type' => $extension,
                                'Size' => $files['size'][$key],
                                'Url' => $path,
                                'Game_record' => $id
                            ]);
                        }
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
                game_record::update('game_record', [
                    'Deleted' => date('Y-m-d H:i:s', time())
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
        $statement = game_record::prepare("
            select Id from game_record order by Id desc limit 1;
        ");
        $statement->execute();
        $id = $statement->fetch();
        return (isset($id['Id'])) ? $id['Id'] + 1 : 1;
    }

    public function getFile($id)
    {
        $file = game_file::findOne('game_file', [
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
