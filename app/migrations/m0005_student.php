<?php

use app\core\Application;

class m0005_student{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `student`  (
            `Id` int NOT NULL COMMENT 'PK, 學號',
            `Name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名',
            `Image` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '大頭照',
            `Introduction` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '介紹',
            `Class_Id` int UNSIGNED NOT NULL COMMENT 'FK, classes.Id',
            `Account` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK, member.Account',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `student_class_id_foreign`(`Class_Id`) USING BTREE,
            INDEX `student_account_foreign`(`Account`) USING BTREE,
            CONSTRAINT `student_account_foreign` FOREIGN KEY (`Account`) REFERENCES `member` (`Account`) ON DELETE CASCADE ON UPDATE RESTRICT,
            CONSTRAINT `student_class_id_foreign` FOREIGN KEY (`Class_Id`) REFERENCES `classes` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `student`;";
        $db->pdo->exec($sql);
    }

}