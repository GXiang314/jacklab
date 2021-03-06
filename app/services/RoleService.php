<?php

namespace app\services;

use app\core\DbModel;
use app\core\Exception\InternalServerErrorException;
use app\model\member;
use app\model\member_role;
use app\model\permission;
use app\model\role;
use app\model\role_permission_group;
use Exception;

class RoleService
{
    public function __construct()
    {
        $this->install();
    }

    public function getAllNoPaging()
    {
        $data = role::get('role');        
        return $data;
    }



    public function getAll($page = 1, $search = null)
    {
        $search = $this->addSlashes($search);
        try{
            $statement = DbModel::prepare("
            select 
                r.* 
            from 
                role as r ".
            (($search != null)?
            " Where 
                r.Name like :search         
            " : "")
            .
            " Order by 
                Id desc 
            "
            .
            " limit " . (($page - 1) * $_ENV['PAGE_ITEM_NUM']) . ", " . ($_ENV['PAGE_ITEM_NUM']) . ";"
            );
            if ($search != null) {
                $statement->bindValue(':search', "%".$search."%");
            }
            $statement->execute();
            $data['list'] = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        $statement = null;        
        $data['page'] = $this->getAllRolePage($search);
        return $data;
    }

    public function getAllRolePage($search = null)
    {
        $search = $this->addSlashes($search);
        try{
            $statement =  DbModel::prepare("
            select count(*) from role "
            .
            (($search != null) ?
                " 
            where 
            Name like :search  
            " : ""
            ));
            if ($search != null) {
                $statement->bindValue(':search', "%".$search."%");
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

    /* #region  ?????????????????? */
    public function add($name, $permissionList)
    {
        try {
            role::create('role', [
                'Id' => $this->newId(),
                'Name' => $name
            ]);

            $data = role::findOne('role', [
                'Name' => $name
            ]);
            foreach ($permissionList as $p) {                
                role_permission_group::create('role_permission_group', [
                    'Role_Id' => $data['Id'],
                    'Permission_group' => $p
                ]);
            }
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        return 'success';
    }
    /* #endregion */

    /* #region  ?????????????????? */
    public function update($id, $permissionList)
    {
        try {
            $role = role::findOne('role', [
                'Id' => $id
            ]);
            if(!empty($role)){
                role_permission_group::delete('role_permission_group', [
                    'Role_Id' => $id
                ]);
                foreach ($permissionList as $p) {
                    role_permission_group::create('role_permission_group', [
                        'Role_Id' => $id,
                        'Permission_group' => $p,
                    ]);
                }
            }else{
                return "??????????????????";
            }
            
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        return 'success';
    }
    /* #endregion */

    /* #region  ???????????????????????????*/
    public function updateMemberRole($account, $role)
    {
        try {
            $data = member::findOne('member', [
                'Account' => $account
            ]);
            if(!empty($data)){
                member_role::delete('member_role', [
                    'Account' => $account
                ]);
                member_role::create('member_role', [
                    'Account' => $account,
                    'Role_Id' => $role,
                ]);
                // foreach ($role as $r) {
                    
                // }
            }else{
                return "????????????????????????";
            }
            
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        return 'success';
    }
    /* #endregion */

    /* #region  ???????????? */
    public function delete($idList)
    {
        try {
            $idList = explode(',', $idList);
            foreach ($idList as $id) {
                // delete project/record
                if (member_role::count('member_role', ['Role_Id' => $id]) > 0) {
                    $name = role::findOne('role', ['Id' => $id])['Name'];
                    return "???{$name}?????????????????????????????????????????????";
                }
            }
            foreach($idList as $id){
                member_role::delete('member_role', [
                    'Role_Id' => $id
                ]);
                role_permission_group::delete('role_permission_group', [
                    'Role_Id' => $id
                ]);
                role::delete('role', [
                    'Id' => $id
                ]);
            }
            
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        return 'success';
    }
    /* #endregion */

    /* #region ???????????????????????????  */

    public function getRole_Permission($id = '')
    {
        try{
            $statement = DbModel::prepare("
            SELECT
                Pg.Id,
                Pg.Name 
            FROM
                permission_group AS Pg
                INNER JOIN role_permission_group AS RP ON RP.Permission_group = Pg.Id
                INNER JOIN role AS R ON R.Id = RP.Role_Id 
            WHERE
                R.Id = :id;        
            ");
            $statement->bindValue(':id', $id);
            $statement->execute();
            $data = $statement->fetchAll(\PDO::FETCH_ASSOC);

        } catch (Exception) {
            throw new InternalServerErrorException();
        }
       
        $statement = null;
        if(!empty($data)){
            $index = 0;
            foreach($data as $row){
                $data[$index++]['list'] = permission::get('permission', [ 'Permission_group' => $row['Id']]);
            }
        }
        return $data;
    }

    public function getPublicRole_Permission($id = '')
    {
        try{
            $statement = DbModel::prepare("
            SELECT
                Pg.Id
            FROM
                permission_group AS Pg
                INNER JOIN role_permission_group AS RP ON RP.Permission_group = Pg.Id
                INNER JOIN role AS R ON R.Id = RP.Role_Id 
            WHERE
                R.Id = :id ;        
            ");
            $statement->bindValue(':id', $id);
            $statement->execute(); 
            $data = $statement->fetchAll(\PDO::FETCH_COLUMN);
        } catch (Exception) {
            throw new InternalServerErrorException();
        }        
        return $data;
    }

    /* #endregion */

    /* #region  ??????????????????????????? */

    public function getMember_Role(string $account)
    {
        try{
            $statement = DbModel::prepare("
            SELECT
                m.Account,
                r.Id as Role_Id,
                r.Name as Role_Name
            FROM
                member AS m
                INNER JOIN member_role AS mr ON mr.Account = m.Account
                INNER JOIN role AS r ON r.Id = mr.Role_Id 
            WHERE
                m.Account = '{$account}';        
            ");
            $statement->execute();
            $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        return $data;
    }

    /* #endregion */

    /* #region  ??????????????????????????? */

    private function install()
    {
        if (role::count('role') == 0) {
            $permissionList = permission::get('permission');
            $roleArray = [
                '?????????',
                '?????????',
            ];
            foreach ($roleArray as $value) {
                role::create('role', [
                    'Id' => $this->newId(),
                    'Name' => $value
                ]);
            }
            foreach ($permissionList as $p) {
                role_permission_group::create('role_permission_group', [
                    'Role_Id' => 1,
                    'Permission_Id' => $p['Id']
                ]);
            }
        }
    }

    /* #endregion */

    public function addSlashes($string = null)
    {
        return  empty($string) ? $string : addslashes($string);
    }

    private function newId()
    {
        try{
            $statement = role::prepare("
                select Id from role order by Id desc limit 1;
            ");
            $statement->execute();
            $id = $statement->fetch();
        } catch (Exception) {
            throw new InternalServerErrorException();
        }
        
        return (isset($id['Id'])) ? $id['Id'] + 1 : 1;
    }
}
