<?php

namespace app\services;

use app\core\DbModel;
use app\core\Exception\InternalServerErrorException;
use app\model\academic;
use app\model\classes;
use Exception;

class AcademicService
{

    public function __construct()
    {
        $this->install();
    }
    public function getAll()
    {        
        $data = academic::get('academic');        
        return $data;
    }

    public function getClass($id)
    {        
        $data = classes::get('classes', [
            'Academic_Id' => $id
        ]);
        return $data;
    }

    public function add($name)
    {        
        academic::create('academic', [
            'Id' => $this->newId(),
            'Name' => $name
        ]);
        return 'success';
    }
    public function update($id, $name)
    {        
        academic::update('academic', [
            'Name' => $name
        ], [
            'Id' => $id
        ]);
        return 'success';
    }
    public function delete($id)
    {        
        classes::delete('classes', [
            'Academic_Id' => $id
        ]);
        academic::delete('academic', [
            'Id' => $id
        ]);
        return 'success';
    }
    private function install()
    {        
        if (academic::count('academic') == 0) {
            $acadmicArray = [
                '碩士班',
                '大學部',
                '二技部',
                '五專部'
            ];
            foreach ($acadmicArray as $value) {
                academic::create('academic', [
                    'Id' => $this->newId(),
                    'Name' => $value
                ]);
            }
        }
    }
    private function newId()
    {
        try {
            $statement = academic::prepare("
                ect Id from academic order by Id desc limit 1;
            ");
            $statement->execute();
            $id = $statement->fetch();
            $statement = null;
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        return (isset($id['Id'])) ? $id['Id'] + 1 : 1;
    }
}
