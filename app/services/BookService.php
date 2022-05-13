<?php

namespace app\services;

use app\core\DbModel;
use app\model\author;
use app\model\book;
use Exception;

class BookService{

    public function getAll()
    {
        $data = book::get('book');
        if(!empty($data)){
            $index = 0;
            foreach($data as $row){
                $data[$index] = $this->getOne($row['Id']);
                $index++;
            }
        }
        return $data;
    }

    public function getOne($id)
    {

        $data = book::get('book',[
            'Id' => $id
        ]);
        
        if(!empty($data)){
            $statement = DbModel::prepare("
            SELECT
            CASE
                    s.`Name` 
                    WHEN s.`Name` THEN
                    s.`Name` ELSE t.NAME 
                END AS `Author`	
            FROM
                book AS b
                LEFT JOIN author AS a ON a.Book_Id = b.Id
                LEFT JOIN student AS s ON s.Account = a.Account
                LEFT JOIN teacher AS t ON t.Account = a.Account
            WHERE
                b.Id = {'$id'};
            ");
            $statement->execute();
            $data['Author'] = $statement->fetchAll(\PDO::FETCH_ASSOC) ?? [];
        }
        return $data;
    }

    public function add($book, $authors, $file)
    {
        try{
            if ($this->checkExtensions($file)) {
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fileName = md5($file['name'] . time()) . '.' . $extension;
                /*
                    temp= explode('.',$file_name);
                    $extension = end($temp);
                */
                $path = str_replace("\\", "\\\\", dirname(dirname(__DIR__)) . "\public\storage\book\\" . $fileName);
                move_uploaded_file($file['tmp_name'], $path); //upload files
                $res = book::create('book', [
                    'Title' => $book['Title'],
                    'Publisher' => $book['Publisher'],
                    'Time' => $book['Time'],
                    'ISBN' => $book['ISBN'],
                    'Image' => $path,
                ]);
                if($res){
                    $bookid = DbModel::findOne('book', [
                        'ISBN' => $book['ISBN']
                    ])['Id'] ?? null;
                    foreach($authors as $author){
                        author::create('author', [
                            'Account' => $author,
                            'Book_Id' => $bookid
                        ]);
                    }                    
                }else{
                    return false;
                }
            } else {
                return "不支援該檔案格式";
            }            
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        return 'success';
    }
    public function update($id, $book, $authors, $file = null)
    {
        try{
            if ($file == null) {
                $res = book::update('book', [
                    'Title' => $book['Title'],
                    'Publisher' => $book['Publisher'],
                    'Time' => $book['Time'],
                    'ISBN' => $book['ISBN'],
                ],[
                    'Id' => $id
                ]);
                if($res){           
                    author::delete('author', ['Book_Id' => $id]);
                    foreach($authors as $author){
                        author::create('author', [
                            'Account' => $author,
                            'Book_Id' => $id
                        ]);
                    }                    
                }else{
                    return false;
                }
            }else{
                if ($this->checkExtensions($file)) {
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $fileName = md5($file['name'] . time()) . '.' . $extension;
                    /*
                        temp= explode('.',$file_name);
                        $extension = end($temp);
                    */
                    $path = str_replace("\\", "\\\\", dirname(dirname(__DIR__)) . "\public\storage\book\\" . $fileName);
                    move_uploaded_file($file['tmp_name'], $path); //upload files
                    $res = book::update('book', [
                        'Title' => $book['Title'],
                        'Publisher' => $book['Publisher'],
                        'Time' => $book['Time'],
                        'ISBN' => $book['ISBN'],
                    ],[
                        'Id' => $id
                    ]);
                    if($res){           
                        author::delete('author', ['Book_Id' => $id]);
                        foreach($authors as $author){
                            author::create('author', [
                                'Account' => $author,
                                'Book_Id' => $id
                            ]);
                        }
                    }else{
                        return false;
                    }
                }else{
                    return "不支援該檔案格式";
                }
            }            
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
                $url = book::findOne('book', [
                    'Id' => $id
                ])['Image'];
                unlink($url);
                author::delete('author', [
                    'Book_Id' => $id
                ]);
                book::delete('book', [
                    'Id' => $id
                ]);       
            }
        }
        catch(Exception $e){
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