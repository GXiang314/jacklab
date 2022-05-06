<?php

use app\core\Application;

class m0015_meeting_file{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `meeting_file`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '檔名',
            `Type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '類型',
            `Url` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '路徑',
            `Meet_Id` int NOT NULL COMMENT 'FK, meeting.Id',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `meeting_file_meet_id_foreign`(`Meet_Id`) USING BTREE,
            CONSTRAINT `meeting_file_meet_id_foreign` FOREIGN KEY (`Meet_Id`) REFERENCES `meeting` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `meeting_file`;";
        $db->pdo->exec($sql);
    }

}