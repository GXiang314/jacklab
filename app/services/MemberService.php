<?php
namespace app\services;

use Exception;
use PDO;
use PDOException;

class MemberService
{
    //after add password to hash
    private $key;
    public function __construct()
    {
        $this->key = $_ENV['SALT_KEY'];
    }
    /* #region  雜湊 */

    private function hash($password)
    {
        return hash('sha256', $password . $this->key);
    }

    /* #endregion */

    /* #region  加入學生 */
    public function studentAdd($member, $role)
    {

        try {
            require_once("./config/conn.php");
            $image = 'member/man.png';
            $time = date('Y-m-d H:i:s', time());
            $sql = "
            insert into member(Account,Password,AuthToken,CreateTime) 
            values(
                '{$member['account']}',
                '{$this->hash($member['password'])}',
                '{$member['token']}',
                '{$time}')
                ;";
            if ($pdo->exec($sql)) {
                $sql = "
                insert into student(Id,Account,Name,Image,Class_Id) 
                values(
                    '{$this->generateStudentId($member['class_Id'])}',
                    '{$member['account']}',
                    '{$member['name']}',
                    '{$image}',
                    '{$member['class_Id']}')
                    ;";
                $sql .= "
                insert into member_rold(Account,Role_Id) 
                values(
                    '{$member['account']}',
                    '{$role}'                   
                    ;";
                $pdo->exec($sql);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $pdo = null;
        return 'success';
    }
    /* #endregion */

    /* #region  加入教師 */
    public function teacherAdd($member, $role)
    {

        try {
            require_once("./config/conn.php");
            $image = 'member/man.png';
            $time = date('Y-m-d H:i:s', time());
            $sql = "
            insert into member(Account,Password,AuthToken,CreateTime) 
            values(
                '{$member['account']}',
                '{$this->hash($member['password'])}',
                '{$member['token']}',
                '{$time}')
                ;";
            $res = $pdo->exec($sql);
            if ($res) {
                $sql = "
                insert into teacher(Id,Account,Name,Title,Image) 
                values(
                    '{$this->generateTeacherId()}',
                    '{$member['account']}',
                    '{$member['name']}',
                    '{$member['title']}',
                    '{$image}')
                    ;";
                $sql .= "
                insert into member_rold(Account,Role_Id) 
                values(
                    '{$member['account']}',
                    '{$role}'                   
                    ;";
                $pdo->exec($sql);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $pdo = null;
        return $res ? 'success' : 'error';
    }
    /* #endregion */

    /* #region  修改個人簡介 */
    public function updateIntroduction(string $account, string $text)
    {
        try {
            require_once("./config/conn.php");
            $sql = "update student set Introduction = '$text' where Account = '$account';";
            $res = $pdo->exec($sql);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $pdo = null;
        return $res ? 'success' : 'error';
    }
    /* #endregion */

    /* #region  刪除會員帳號 */
    public function delete($idList)
    {
        $idList = explode(',', $idList);
        require_once("./config/conn.php");        
        foreach($idList as $id){
            $data = $this->getMemberData($id);
            if (isset($data['Account'])) {
                $sql = "delete from game_member where Student_Id = '{$id}';";
                $sql .= "delete from meeting_member where Account = '{$data['Account']}';";
                $sql .= "delete from proj_group_member where Student_Id = '{$id}';";
                $sql .= "delete from member_role where Account = '{$data['Account']}';";
                $sql .= "delete from student where Account = '{$data['Account']}';";
                $sql .= "delete from teacher where Account = '{$data['Account']}';";
                $sql .= "delete from member where Account = '{$data['Account']}';";
                $res = $pdo->exec($sql);
            }
        }
        $pdo = null;
        return $res ? 'success' : 'error';
    }
    /* #endregion */

    /* #region  產生密碼 */
    public function generatePassword($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $pwd = '';
        for ($i = 0; $i < $length; $i++) {
            $pwd .= $characters[rand(0, $charactersLength - 1)];
        }
        return $pwd;
    }
    /* #endregion */

    /* #region  產生學號 */
    private function generateStudentId($class_Id)
    {
        $str = (intval(date("Y")) - 1911) . str_pad($class_Id, 2, '0', STR_PAD_LEFT);
        try {
            require_once("./config/conn.php");
            $sql = "select Id from student where Id like '%$str%' order by Id desc limit 1;";
            $data = $pdo->query($sql);
            if (isset($data)) {
                return intval($data['Id']) + 1;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
        $pdo = null;
        return intval($str . str_pad('0', 3, STR_PAD_LEFT)) + 1;
    }
    /* #endregion */

    /* #region  產生教師編號 */
    private function generateTeacherId()
    {
        require_once("./config/conn.php");
        $str = str_pad('1', 8, '7', STR_PAD_RIGHT);
        $sql = "select Id from teacher where Id like '%$str%' order by Id desc limit 1;";
        $data = $pdo->query($sql);

        if (isset($data)) {
            return intval($data['Id']) + 1;
        }
        $pdo = null;
        return intval($str) + 1;
    }
    /* #endregion */

    /* #region  產生信箱驗證碼 */
    public function generateAuthToken($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $AuthToken = '';
        for ($i = 0; $i < $length; $i++) {
            $AuthToken .= $characters[rand(0, $charactersLength - 1)];
        }
        return $AuthToken;
    }
    /* #endregion */

    /* #region  信箱驗證處理 */

    public function emailTokenCheck($account, $token)
    {
        try {
            require_once("./config/conn.php");
            $sql = "
            update member 
            set Authtoken = '' 
            where Account = '$account';
            ";
            $res = $pdo->exec($sql);
            $pdo = null;
        } catch (PDOException $e) {
            return false;
        }
        return $res;
    }
    /* #endregion */

    /* #region  取得個人資料 */

    public function getMemberData($student_id)
    {
        try {
            require_once("./config/conn.php");
            $sql = "
            select * from student as s, member as m 
            where 
                s.Account = m.Account and
                s.Id = $student_id 
            limit 1;";
            $data =  $pdo->query($sql);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $pdo = null;
        return $data;
    }

    public function getAccountData($account)
    {
        try {
            require_once("./config/conn.php");
            $sql = "
            select * from member as m, student as s 
            where 
                s.Account = m.Account and
                m.Account = $account 
            limit 1;";
            $data =  $pdo->query($sql);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $pdo = null;
        return $data;
    }

    /* #endregion */

    /* #region  取得所有會員資料 */
    public function getAllMember()
    {
        require_once("../config/conn.php");
        $sql = "
        select s.*, m.CreateTime as CreateTime, r.Id as Role_Id,r.`Name` as Role_Name 
        from member as m, student as s, member_role as mr, role as r
        where
            s.Account = m.Account and
            m.Account = mr.Account and 
            mr.Role_Id = r.Id;            
        ";
        $datalist = [];
        foreach( $pdo->query($sql) as $row){
            $data['Id'] = $row['Id'];
            $data['Name'] = $row['Name'];
            $data['Image'] = $row['Image'];
            $data['Introduction'] = $row['Introduction'];
            $data['Class_Id'] = $row['Class_Id'];
            $data['Account'] = $row['Account'];
            $data['CreateTime'] = $row['CreateTime'];
            $data['Role_Id'] = $row['Role_Id'];
            $data['Role_Name'] = $row['Role_Name'];
            $datalist[] = $data;
        }
        $pdo = null;
        return $datalist;
    }
    /* #endregion */

    /* #region  登入密碼確認 */
    public function passwordCheck(string $account, string $password)
    {
        $data = $this->getAccountData($account);
        if (isset($data)) {
            if ($data['Password'] == $this->hash($password)) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
    /* #endregion */

    /* #region  信箱是否驗證 */
    public function isEmailValidate($account)
    {
        $data = $this->getAccountData($account);
        if ($data) {
            if ($data['AuthToken'] == '') {
                return true;
            }
        }
        return false;
    }
    /* #endregion */

    /* #region  修改密碼 */
    public function updateUserPassword(string $account, string $new)
    {
        require_once("./config/conn.php");
        $sql = "update member set Password = '{$this->hash($new)}' where Account = $account;";
        $res = $pdo->exec($sql);
        $pdo = null;
        return $res;
    }

    public function updatePassword(string $account, string $old, string $new)
    {
        if ($this->passwordCheck($account, $old)) {
            require_once("./config/conn.php");
            $sql = "update member set Password = '{$this->hash($new)}' where Account = $account;";
            $res = $pdo->exec($sql);
            $pdo = null;
            return $res;
        }
        return '舊密碼輸入錯誤';
    }
    /* #endregion */

    /* #region  載入管理員帳號 */
    private function install()
    {   
        require_once("./config/conn.php");
        $sql = "select count(*) from memebr;";
        $res = false;
        if($pdo->query($sql) == 0){
            $image = 'member/man.png';
            $time = date("Y-m-d H:i:s", time());
            $acc = 'jacklab';
            $pwd = '0921730662';
            $sql = "insert into member(Account,Password,AuthCode,CreateTime) 
            values('{$acc}','{$this->hash($pwd)}','','$time');";
            if($pdo->exec($sql)){
                $sql = "insert into teacher(Id,Account,Name,Title,Introduction,Image) 
                values('{$this->generateTeacherId()}','{$acc}','姜琇森','教授','','{$image}');";
                $sql .= "insert into member_role(Account,Role_Id) 
                values('{$acc}','1');";
                $res = $pdo->exec($sql);
            }            
        }
        $pdo = null;
        return $res;
    }
    /* #endregion */
}
