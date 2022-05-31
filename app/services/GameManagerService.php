<?php

namespace app\services;

use app\core\DbModel;
use app\core\Exception\InternalServerErrorException;
use app\model\game_record;
use app\model\game_type;
use Exception;

class GameManagerService
{

    public function __construct()
    {
        $this->install();
    }
    public function getAll()
    {
        $data = game_type::get('game_type');        
        return $data;
    }

    public function getRecord($id)
    {
        $data = game_record::get('game_record', [
            'Game_type' => $id
        ]);
        return $data;
    }

    public function add(string $name)
    {
        game_type::create('game_type', [
            'Id' => $this->newId(),
            'Name' => $name,
        ]);
        return 'success';
    }
    public function update($id, $name)
    {
        game_type::update('game_type', [
            'Name' => $name
        ], [
            'Id' => $id
        ]);
        return 'success';
    }
    public function delete($id)
    {
        // delete student
        game_type::delete('game_type', [
            'Id' => $id
        ]);
        return 'success';
    }

    public function install()
    {
        if (game_type::count('game_type') == 0) {
            $typeArray = [
                '大專生國科會計畫',
                '資訊應用服務創新競賽'
            ];
            foreach ($typeArray as $value) {
                game_type::create('game_type', [
                    'Id' => $this->newId(),
                    'Name' => $value
                ]);
            }
        }
    }

    private function newId()
    {
        try{
            $statement = game_type::prepare("
                select Id from game_type order by Id desc limit 1;
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
