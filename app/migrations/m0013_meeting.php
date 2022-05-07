<?php

use app\core\Application;

class m0013_meeting{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE if not exists `meeting`  (
            `Id` int NOT NULL COMMENT 'PK',
            `Title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '標題',
            `Content` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '內文',
            `Time` datetime NOT NULL COMMENT '會議時間',
            `Place` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '會議地點',
            `Uploader` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK, member.Account',
            `Deleted` datetime NULL DEFAULT NULL COMMENT '軟刪除',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `meeting_uploader_foreign`(`Uploader`) USING BTREE,
            CONSTRAINT `meeting_uploader_foreign` FOREIGN KEY (`Uploader`) REFERENCES `member` (`Account`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `meeting`;";
        $db->pdo->exec($sql);
    }

}