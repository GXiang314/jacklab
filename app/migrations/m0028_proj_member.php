<?php

use app\core\Application;

class m0028_proj_member{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `proj_member`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Project_Id` int UNSIGNED NOT NULL COMMENT 'FK, project.Id',
            `Account` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK, member.Account',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `meeting_member_account_foreign`(`Account`) USING BTREE,
            INDEX `Project_Id`(`Project_Id`) USING BTREE,
            CONSTRAINT `proj_member_ibfk_1` FOREIGN KEY (`Account`) REFERENCES `member` (`Account`) ON DELETE CASCADE ON UPDATE RESTRICT,
            CONSTRAINT `proj_member_ibfk_2` FOREIGN KEY (`Project_Id`) REFERENCES `project` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = COMPACT;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `proj_member`;";
        $db->pdo->exec($sql);
    }

}