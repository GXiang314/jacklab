<?php

use app\core\Application;

class m0020_proj_file{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE if not exists `proj_file`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '檔名',
            `Type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '類型',
            `Size` bigint UNSIGNED NOT NULL COMMENT '大小',
            `Url` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '路徑',
            `Proj_record` int NOT NULL COMMENT 'FK, proj_record.Id',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `proj_file_proj_record_foreign`(`Proj_record`) USING BTREE,
            CONSTRAINT `proj_file_proj_record_foreign` FOREIGN KEY (`Proj_record`) REFERENCES `proj_record` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `proj_file`;";
        $db->pdo->exec($sql);
    }

}