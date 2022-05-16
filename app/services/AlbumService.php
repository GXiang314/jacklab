<?php

namespace app\services;

use app\core\DbModel;
use app\model\album;
use Exception;

class AlbumService
{

    public function getAll()
    {
        $data = album::get('album');
        return $data;
    }

    public function getOne($id)
    {

        $data = album::findOne('album', [
            'Id' => $id
        ]);
        return $data;
    }

    public function add($title, $file)
    {
        try {
            if ($this->checkExtensions($file)) {
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fileName = md5($file['name'] . time()) . '.' . $extension;
                /*
                    temp= explode('.',$file_name);
                    $extension = end($temp);
                */
                $path = "\storage\album\\" . $fileName ;
                move_uploaded_file($file['tmp_name'], dirname(dirname(__DIR__)) . "\public".$path); //upload files
                album::create('album', [
                    'Title' => $title,
                    'CreateTime' => date("Y-m-d h:i:s"),
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
                    $fileName = md5($file['name'] . time()) . '.' . $extension;
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
