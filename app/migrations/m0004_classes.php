<?php

use app\core\Application;

class m0004_classes{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `classes`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '班級名稱',
            `Academic_Id` int UNSIGNED NOT NULL COMMENT 'FK, academic.Id',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `classes_academic_id_foreign`(`Academic_Id`) USING BTREE,
            CONSTRAINT `classes_academic_id_foreign` FOREIGN KEY (`Academic_Id`) REFERENCES `academic` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `classes`;";
        $db->pdo->exec($sql);
    }

}