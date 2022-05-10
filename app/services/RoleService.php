<?php

namespace app\services;

use app\core\DbModel;
use app\model\member;
use app\model\member_role;
use app\model\permission;
use app\model\role;
use app\model\role_permission;
use Exception;

class RoleService
{
    public function __construct()
    {
        $this->install();
    }
    public function getAll()
    {
        $data = role::get('role');
        return $data;
    }

    /* #region  新增角色權限 */
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
                role_permission::create('role_permission', [
                    'Role_Id' => $data['Id'],
                    'Permission_Id' => $p
                ]);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return 'success';
    }
    /* #endregion */

    /* #region  修改角色權限 */
    public function update($id, $permissionList)
    {
        try {
            $role = role::findOne('role', [
                'Id' => $id
            ]);
            if(!empty($role)){
                role_permission::delete('role_permission', [
                    'Role_Id' => $id
                ]);
                foreach ($permissionList as $p) {
                    role_permission::create('role_permission', [
                        'Role_Id' => $id,
                        'Permission_Id' => $p,
                    ]);
                }
            }else{
                return "該角色不存在";
            }
            
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return 'success';
    }
    /* #endregion */

    /* #region  修改使用者權限角色*/
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
                return "該會員帳號不存在";
            }
            
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return 'success';
    }
    /* #endregion */

    /* #region  刪除角色 */
    public function delete($id)
    {
        try {
            member_role::delete('member_role', [
                'Role_Id' => $id
            ]);
            role_permission::delete('role_permission', [
                'Role_Id' => $id
            ]);
            role::delete('role', [
                'Id' => $id
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return 'success';
    }
    /* #endregion */

    /* #region 取得角色對應權限  */

    public function getRole_Permission(int $id)
    {
        $statement = DbModel::prepare("
        SELECT
            P.Id,
            P.Name 
        FROM
            PERMISSION AS P
            INNER JOIN role_permission AS RP ON RP.Permission_Id = P.Id
            INNER JOIN role AS R ON R.Id = RP.Role_Id 
        WHERE
            R.Id = '{$id}';        
        ");
        $statement->execute();
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }

    /* #endregion */

    /* #region  取得使用者對應角色 */

    public function getMember_Role(string $account)
    {
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
        return $data;
    }

    /* #endregion */

    /* #region  預載入角色權限清單 */

    private function install()
    {
        if (role::count('role') == 0) {
            $permissionList = permission::get('permission');
            $roleArray = [
                '管理者',
                '研究生',
                '專題生',
            ];
            foreach ($roleArray as $value) {
                role::create('role', [
                    'Id' => $this->newId(),
                    'Name' => $value
                ]);
            }
            foreach ($permissionList as $p) {
                role_permission::create('role_permission', [
                    'Role_Id' => 1,
                    'Permission_Id' => $p['Id']
                ]);
            }
        }
    }

    /* #endregion */

    private function newId()
    {
        $statement = role::prepare("
            select Id from role order by Id desc limit 1;
        ");
        $statement->execute();
        $id = $statement->fetch();
        return (isset($id['Id'])) ? $id['Id'] + 1 : 1;
    }
}
