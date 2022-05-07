<?php

use app\core\Application;

class m0014_meeting_tag{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE if not exists `meeting_tag`  (
            `Id` int UNSIGNED NOT NULL COMMENT 'PK',
            `Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名稱',
            `Meet_Id` int NOT NULL COMMENT 'FK, meeting.Id',
            PRIMARY KEY (`Meet_Id`, `Id`) USING BTREE,
            INDEX `meeting_tag_tag_id_foreign`(`Id`) USING BTREE,
            CONSTRAINT `meeting_tag_meet_id_foreign` FOREIGN KEY (`Meet_Id`) REFERENCES `meeting` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `meeting_tag`;";
        $db->pdo->exec($sql);
    }

}