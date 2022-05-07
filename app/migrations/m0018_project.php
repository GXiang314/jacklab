<?php

use app\core\Application;

class m0018_project{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE if not exists `project`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '專案名稱',
            `Description` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '描述',
            `CreateTime` datetime NOT NULL COMMENT '建立時間',
            `Proj_type` int UNSIGNED NOT NULL COMMENT 'FK, proj_type.Id',
            `Creater` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK, member.Account',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `project_creater_foreign`(`Creater`) USING BTREE,
            INDEX `project_proj_type`(`Proj_type`) USING BTREE
          ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `project`;";
        $db->pdo->exec($sql);
    }

}