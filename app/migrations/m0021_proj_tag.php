<?php

use app\core\Application;

class m0021_proj_tag{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `proj_tag`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名稱',
            `Project_Id` int UNSIGNED NOT NULL COMMENT 'FK, project.Id',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `proj_tag_project_id_foreign`(`Project_Id`) USING BTREE,
            CONSTRAINT `proj_tag_project_id_foreign` FOREIGN KEY (`Project_Id`) REFERENCES `project` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `proj_tag`;";
        $db->pdo->exec($sql);
    }

}