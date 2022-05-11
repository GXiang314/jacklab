<?php

namespace app\services;

use app\core\Application;
use app\core\DbModel;
use app\model\member;
use app\model\member_role;
use app\model\student;
use app\model\teacher;
use Exception;
use PDOException;

class MemberService
{
    //after add password to hash
    private static $key;
    public function __construct()
    {
        self::$key = $_ENV['SALT_KEY'];
        $this->install();
    }
    /* #region  雜湊 */

    public static function hash($password)
    {
        return hash('sha256', $password . self::$key);
    }

    /* #endregion */

    /* #region  加入學生 */
    public function studentAdd($request)
    {
        try {
            $member = new member();
            $student = new student();
            $member_rold = new member_role();
            $member->loadData($request);
            $student->loadData($request);
            $member_rold->loadData($request);

            $name = $student->Name;
            $acc = $member->Account;
            $pwd = $member->Password;
            $token = $member->AuthToken;

            if ($res = $member->save()) {
                $mailService = new MailService();
                $student->save();
                $member_rold->save();
                $mailService->sendRegisterMail($name, $acc, $pwd, $token);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $res ? 'success' : false;
    }
    /* #endregion */

    /* #region  加入教師 */
    public function teacherAdd($request)
    {
        try {
            $member = new member();
            $teacher = new teacher();
            $member_rold = new member_role();
            $member->loadData($request);
            $teacher->loadData($request);
            $member_rold->loadData($request);
            if ($res = $member->save()) {
                $teacher->save();
                $member_rold->save();
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $res ? 'success' : 'error';
    }
    /* #endregion */

    /* #region  修改個人簡介 */
    public function updateIntroduction(string $account, string $text)
    {
        try {
            $res = DbModel::update('student', ['Introduction' => $text], ['Account' => $account]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $res ? 'success' : 'error';
    }
    /* #endregion */

    /* #region  修改學生個人大頭貼 */
    public function updatePhoto(string $account, $file)
    {
        try {
            if ($this->checkExtensions($file)) {
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fileName = md5($file['name'] . time()) . '.' . $extension;
                /*
                    temp= explode('.',$file_name);
                    $extension = end($temp);
                */
                $path = str_replace("\\", "\\\\", dirname(dirname(__DIR__)) . "\public\storage\member\\" . $fileName);
                move_uploaded_file($file['tmp_name'], $path); //upload files
                $res = DbModel::update('student', ['Image' => $path], ['Account' => $account]);
            }else{
                return "不支援該檔案格式";
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $res ? 'success' : 'error';
    }
    /* #endregion */

    /* #region  修改教師大頭貼 */
    public function updateTeacherPhoto(string $account, $file)
    {
        try {
            if ($this->checkExtensions($file)) {
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fileName = md5($file['name'] . time()) . '.' . $extension;
                /*
                    temp= explode('.',$file_name);
                    $extension = end($temp);
                */
                $path = str_replace("\\", "\\\\", dirname(dirname(__DIR__)) . "\public\storage\member\\" . $fileName);
                move_uploaded_file($file['tmp_name'], $path); //upload files
                $res = DbModel::update('teacher', ['Image' => $path], ['Account' => $account]);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $res ? 'success' : 'error';
    }
    /* #endregion */

    /* #region  修改密碼 */
    public function updateUserPassword(string $account, string $new)
    {
        $res = DbModel::update('member', [
            'Password' => $this->hash($new)
        ], [
            'Account' => $account
        ]);
        return $res;
    }

    public function updatePassword(string $account, string $old, string $new)
    {
        if ($this->passwordCheck($account, $old)) {
            $res = DbModel::update('member', [
                'Password' => $this->hash($new)
            ], [
                'Account' => $account
            ]);
            return ($res) ? 'success' : 'error';
        }
        return '舊密碼輸入錯誤';
    }
    /* #endregion */

    /* #region  修改學生班級 */
    public function updateStudentClass(string $account, string $class_Id)
    {
        $res = DbModel::update('student', [
            'Class_Id' => $class_Id
        ], [
            'Account' => $account
        ]);
        return $res;
    }
    /* #endregion */

    /* #region  刪除會員帳號 */
    public function delete($idList)
    {
        $idList = explode(',', $idList);
        try {
            foreach ($idList as $id) {
                $data = $this->getMemberData($id);
                if (isset($data['Account'])) {
                    Application::$app->db->pdo->exec("
                        delete from game_member where Student_Id = '{$id}';
                        delete from meeting_member where Account = '{$data['Account']}';
                        delete from member_role where Account = '{$data['Account']}';
                        delete from student where Account = '{$data['Account']}';
                        delete from teacher where Account = '{$data['Account']}';
                        delete from member where Account = '{$data['Account']}';
                    ");
                }
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return 'success';
    }
    /* #endregion */

    /* #region  產生密碼 */
    public static function generatePassword($length = 10)
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
    public static function generateStudentId($class_Id)
    {
        $str = (intval(date("Y")) - 1911) . str_pad($class_Id, 2, '0', STR_PAD_LEFT);
        try {
            $student = new student();
            $statement = $student->prepare(
                "
                select Id from student where Id like '%$str%' order by Id desc limit 1;"
            );
            $statement->execute();
            $data = $statement->fetch(\PDO::FETCH_ASSOC);
            if (!empty($data)) {
                return intval($data['Id']) + 1;
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
        return intval($str . str_pad('0', 3, STR_PAD_LEFT)) + 1;
    }
    /* #endregion */

    /* #region  產生教師編號 */
    public static function generateTeacherId()
    {
        $str = str_pad('1', 8, '7', STR_PAD_RIGHT);
        $teacher = new teacher();
        $statement = $teacher->prepare(
            "
            select Id from teacher where Id like '%$str%' order by Id desc limit 1;"
        );
        $statement->execute();
        $data = $statement->fetch(\PDO::FETCH_ASSOC);
        if (!empty($data)) {
            return intval($data['Id']) + 1;
        }
        return intval($str) + 1;
    }
    /* #endregion */

    /* #region  產生信箱驗證碼 */
    public static function generateAuthToken($length = 10)
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
            $data = $this->getAccountData($account);
            if (!empty($data)) {
                if ($data['AuthToken'] == $token) {
                    DbModel::update('member', ['AuthToken' => ''], ['Account' => $account]);
                } else {
                    return '驗證碼錯誤，請重新驗證';
                }
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return 'success';
    }
    /* #endregion */

    /* #region  取得個人資料 */

    public function getMemberData($student_id)
    {
        try {
            $statement = DbModel::prepare("
            select s.*, m.* from student as s, member as m 
            where 
                s.Account = m.Account and
                s.Id = '$student_id' 
            limit 1;");
            $statement->execute();
            $data = $statement->fetch(\PDO::FETCH_ASSOC);
            if (!empty($data)) {
                $roldSelect = DbModel::prepare("         
                select r.* from role as r, member_role as mr, student as s
                where 
                    r.Id = mr.Role_ID and
                    mr.Account = s.Account;
                    s.Id = '$student_id';
                ");
                $roldSelect->execute();
                $data['role'] = $roldSelect->fetchAll(\PDO::FETCH_ASSOC); //role
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $data;
    }

    public function getAccount($account)
    {
        try {
            $statement = DbModel::prepare("         
            SELECT
                m.*,
                CASE s.`Name` WHEN s.`Name` THEN s.`Name` ELSE t.NAME END as Name		
            FROM
                member AS m 
                LEFT JOIN student AS s ON s.Account = m.Account
                LEFT JOIN teacher AS t ON t.Account = m.Account 
            WHERE
                m.Account = '{$account}';");
            $statement->execute();
            $data =  $statement->fetch(\PDO::FETCH_ASSOC); //member
            if (!empty($data)) {
                $statement = DbModel::prepare("         
                SELECT
                    r.Id,
                    r.Name
                FROM
                    member AS m 
                    INNER JOIN member_role AS mr ON mr.Account = m.Account
                    INNER JOIN role AS r ON r.Id = mr.Role_Id 
                WHERE
                    m.Account = '{$account}';");
                $statement->execute();
                $role =  $statement->fetchAll(\PDO::FETCH_ASSOC); //member
                $data['Role'] = $role;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $data;
    }

    public function getAccountData($account)
    {
        try {
            $statement = DbModel::prepare("         
            select s.*, m.* from member as m, student as s 
            where 
                s.Account = m.Account and
                m.Account = '$account' 
            limit 1;");
            $statement->execute();
            $data =  $statement->fetch(\PDO::FETCH_ASSOC); //member
            if (!empty($data)) {
                $roldSelect = DbModel::prepare("         
                select r.* from role as r, member_role as mr 
                where 
                    r.Id = mr.Role_ID and
                    mr.Account = '$account';
                ");
                $roldSelect->execute();
                $data['role'] = $roldSelect->fetchAll(\PDO::FETCH_ASSOC); //role
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $data;
    }

    /* #endregion */

    /* #region  取得所有會員資料 */
    public function getAllMember()
    {
        $member = new Member();
        $statement = $member->prepare("
        SELECT
            s.*,
            m.CreateTime AS CreateTime,
            r.Id AS Role_Id,
            r.`Name` AS Role_Name,
            c.Name as Class_Name
        FROM
            member AS m,
            student AS s,
            member_role AS mr,
            role AS r,
            classes as c
        WHERE
            s.Account = m.Account 
            AND m.Account = mr.Account 
            AND mr.Role_Id = r.Id
            AND s.Class_Id = c.Id;");
        $statement->execute();
        $datalist = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $datalist;
    }
    /* #endregion */

    /* #region  取得所有會員公開資料 */
    public function getPublicAllMember()
    {
        $member = new Member();
        $statement = $member->prepare("
        SELECT
            m.Account,
        CASE
                s.`Name` 
                WHEN s.`Name` THEN
                s.`Name` ELSE t.NAME 
            END AS `Name`,
            role.NAME AS Role_Name,
            m.CreateTime AS CreateTime,
        CASE
                s.`Image` 
                WHEN s.`Image` THEN
                s.`Image` ELSE t.`Image` 
            END AS Image 
        FROM
            member AS m
            INNER JOIN member_role AS mr ON mr.Account = m.Account
            INNER JOIN role ON role.Id = mr.Role_Id
            LEFT JOIN student AS s ON s.Account = m.Account
            LEFT JOIN teacher AS t ON t.Account = m.Account      
        ");
        $statement->execute();
        $datalist = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $datalist;
    }
    /* #endregion */

    /* #region  取得公開個人資料 */

    public function getPublicMember($student_id)
    {
        try {
            $statement = DbModel::prepare("
            select s.Id, s.Name,s.Introduction, s.Image, s.Account, m.CreateTime, c.Name as ClassName from student as s, member as m, classes as c
            where 
                s.Account = m.Account and
                s.Id = '$student_id' and
                s.Class_Id = c.Id 
            limit 1;");
            $statement->execute();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    /* #endregion */

    /* #region  登入密碼確認 */
    public function passwordCheck(string $account, string $password)
    {
        $data = $this->getAccount($account);
        if (!empty($data)) {
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
        $data = $this->getAccount($account);
        if ($data) {
            if (empty($data['AuthToken'])) {
                return true;
            }
        }
        return false;
    }
    /* #endregion */

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

    /* #region  載入管理員帳號 */
    private function install()
    {
        $member = new member();
        $data = [
            'Account' => 'jacklab',
            'Password' => '0921730662',
            'AuthToken' => '',
            'IsAdmin' => true,
            'Name' => '姜琇森',
            'Title' => '教授',
            'Role_Id' => 1
        ];
        if ($member->count('member') == 0) {
            $member->loadData($data);
            if ($member->save()) {
                $teacher = new teacher();
                $member_rold = new member_role();
                $teacher->loadData($data);
                $member_rold->loadData($data);
                $teacher->save();
                $res = $member_rold->save();
            }
        }
        return $res ?? false;
    }
    /* #endregion */
}
