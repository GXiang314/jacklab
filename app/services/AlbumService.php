<?php

namespace app\services;

use app\core\DbModel;
use app\model\album;
use Exception;
use php_user_filter;

class AlbumService
{

    public function select()
    {
        $statement = DbModel::prepare("
        select 
            a.* 
        from 
            album as a
        order by 
            a.CreateTime desc;    
        ");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAll($page = 1, $search = null)
    {
        $search = $this->addSlashes($search);
        $statement = DbModel::prepare("
        select 
            a.* 
        from 
            album as a ".
        (($search != null)?
        " Where 
            a.Title like :search         
        " : "").
        "
        Order by a.CreateTime desc
        
        ".
        " limit " . (($page - 1) * $_ENV['PAGE_ITEM_NUM']) . ", " . ($_ENV['PAGE_ITEM_NUM']) . ";"
        );
        if ($search != null){
            $statement->bindValue(':search', "%".$search."%");
        }
        $statement->execute();
        $data['list'] = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $index = 0;
        if($data['list']){
            foreach($data['list'] as $row){
                $data['list'][$index]['File'] = pathinfo($row['Image'], PATHINFO_FILENAME).".".pathinfo($row['Image'], PATHINFO_EXTENSION) ;
                $index++;
            }
        }
        
        $data['page'] = $this->getAllAlbumPage($search);
        return $data;
    }

    public function getOne($id)
    {

        $data = album::findOne('album', [
            'Id' => $id
        ]);
        return $data;
    }

    public function getAllAlbumPage($search = null)
    {
        $search = $this->addSlashes($search);
        $statement =  DbModel::prepare("
        select count(*) from album "
        .
        (($search != null) ?
            " 
        where 
            Title like :search 
        " : ""
        ));
        if ($search != null){
            $statement->bindValue(':search', "%".$search."%");
        }
        $statement->execute();
        $count = $statement->fetchColumn();
        $page = ceil((float)$count / $_ENV['PAGE_ITEM_NUM']);
        return $page == 0 ? 1 : $page;
    }

    public function add($title, $file)
    {
        try {
            if ($this->checkExtensions($file)) {
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fileName = pathinfo($file['name'], PATHINFO_FILENAME).date("ymdhis", time()) . '.' . $extension;
                /*
                    temp= explode('.',$file_name);
                    $extension = end($temp);
                */
                $path = "\storage\album\\" . $fileName ;
                move_uploaded_file($file['tmp_name'], dirname(dirname(__DIR__)) . "\public".$path); //upload files
                album::create('album', [
                    'Title' => $title,
                    'CreateTime' => date("Y-m-d H:i:s"),
                    'Image' => $path
                ]);
            } else {
                return "不支援該檔案格式";
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return 'success';
    }
    public function update($id, $title, $file = null)
    {
        try {
            if ($file == null) {
                album::update('album', [
                    'Title' => $title
                ], [
                    'Id' => $id
                ]);
            } else {
                if ($this->checkExtensions($file)) {
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $fileName = pathinfo($file['name'], PATHINFO_FILENAME).date("ymdhis", time()) . '.' . $extension;
                    /*
                        temp= explode('.',$file_name);
                        $extension = end($temp);
                    */
                    $path = "\storage\album\\" . $fileName;
                    move_uploaded_file($file['tmp_name'], dirname(dirname(__DIR__)) . "\public".$path);
                    unlink(
                        dirname(dirname(__DIR__)) . "\public" . album::findOne('album', [
                            'Id' => $id
                        ])['Image'] ?? ''
                    );
                    album::update('album', [
                        'Title' => $title,
                        'Image' => $path
                    ], [
                        'Id' => $id
                    ]);
                } else {
                    return "不支援該檔案格式";
                }
            }
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
                $url = album::findOne('album', [
                    'Id' => $id
                ])['Image'] ?? '';
                if(file_exists(dirname(dirname(__DIR__)) . "\public".$url)){
                    unlink(dirname(dirname(__DIR__)) . "\public".$url);
                }

                album::delete('album', [
                    'Id' => $id
                ]);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return 'success';
    }

    public function addSlashes($string = null)
    {
        return  empty($string) ? $string : addslashes($string);
    }

    /* #region  驗證副檔名 */
    public function checkExtensions($file = null)
    {
        if ($file == null) return false;
        $allow_extensions = explode(',', "png,jpeg,jpg");
        $check_Array = [];
        $check_Array[] = pathinfo($file['name'], PATHINFO_EXTENSION);
        $diff = array_diff($check_Array, $allow_extensions);
        return empty($diff);
    }
    /* #endregion */
}
