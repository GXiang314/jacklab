<?php

namespace app\services;

use app\core\DbModel;
use app\model\author;
use app\model\book;
use Exception;

class BookService{

    public function getAll($page = 1, $search = null)
    {
        $statement = book::prepare("
        SELECT
            b.* 
        FROM
            book AS b "
        .(($search != null)?
        " Where 
            b.Title like '%$search%' or 
            b.Publisher like '%$search%' or 
            b.ISBN like '%$search%' or 
            b.Time like '%$search%' 
        ": "").
        " limit " . (($page - 1) * $_ENV['PAGE_ITEM_NUM']) . ", " . ($_ENV['PAGE_ITEM_NUM']) . ";"
        );
        $statement->execute();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if(!empty($data)){
            $index = 0;
            foreach($data as $row){
                $data[$index]['Authors'] = $this->getAuthors($row['Id']);
                $index++;
            }
        }
        return $data;
    }

    public function getOne($id)
    {

        $data = book::findOne('book',[
            'Id' => $id
        ]);
        
        if(!empty($data)){
            $data['Authors'] = $this->getAuthors($id);
        }
        return $data;
    }

    public function getAuthors($id)
    {
        $statement = DbModel::prepare("
            SELECT
            a.Account as account, 
            CASE
                    s.`Name` 
                    WHEN s.`Name` THEN
                    s.`Name` ELSE t.NAME 
                END AS `name`	
            FROM
                book AS b
                LEFT JOIN author AS a ON a.Book_Id = b.Id
                LEFT JOIN student AS s ON s.Account = a.Account
                LEFT JOIN teacher AS t ON t.Account = a.Account
            WHERE
                b.Id = '{$id}';
            ");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC) ?? [];
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
                $path = "\storage\book\\" . $fileName;
                move_uploaded_file($file['tmp_name'], dirname(dirname(__DIR__)) . "\public".$path); //upload files
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
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        return 'success';
    }

    public function UpdateImage($id, $file)
    {
        try{
            if ($this->checkExtensions($file)) {
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fileName = md5($file['name'] . time()) . '.' . $extension;
                /*
                    temp= explode('.',$file_name);
                    $extension = end($temp);
                */
                $path = "\storage\book\\" . $fileName;
                move_uploaded_file($file['tmp_name'], dirname(dirname(__DIR__)) . "\public".$path); //upload files
                unlink(
                    dirname(dirname(__DIR__)) . "\public".
                    book::findOne('book', [ 'Id' => $id ])['Image'] ?? ''
                );
                book::update('book', [
                    'Image' => $path
                ],[
                    'Id' => $id
                ]);
                
            }else{
                return "不支援該檔案格式";
            }
        }catch(Exception $e){
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
                ])['Image'] ?? '';
                if(file_exists(dirname(dirname(__DIR__)) . "\public".$url)){
                    unlink(dirname(dirname(__DIR__)) . "\public".$url);
                }
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