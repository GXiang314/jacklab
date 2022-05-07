<?php

use app\core\Application;

class m0002_teacher{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE if not exists `teacher`  (
            `Id` int NOT NULL COMMENT 'PK, 編號',
            `Name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名',
            `Image` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '大頭照',
            `Title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '職稱',
            `Introduction` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '介紹',
            `Account` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK, member.Account',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `teacher_account_foreign`(`Account`) USING BTREE,
            CONSTRAINT `teacher_account_foreign` FOREIGN KEY (`Account`) REFERENCES `member` (`Account`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `teacher`;";
        $db->pdo->exec($sql);
    }

}