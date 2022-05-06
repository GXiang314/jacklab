<?php

use app\core\Application;

class m0016_meeting_member{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `meeting_member`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Meet_Id` int NOT NULL COMMENT 'FK, meeting.Id',
            `Account` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK, member.Account',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `meeting_member_meet_id_foreign`(`Meet_Id`) USING BTREE,
            INDEX `meeting_member_account_foreign`(`Account`) USING BTREE,
            CONSTRAINT `meeting_member_account_foreign` FOREIGN KEY (`Account`) REFERENCES `member` (`Account`) ON DELETE CASCADE ON UPDATE RESTRICT,
            CONSTRAINT `meeting_member_meet_id_foreign` FOREIGN KEY (`Meet_Id`) REFERENCES `meeting` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `meeting_member`;";
        $db->pdo->exec($sql);
    }

}