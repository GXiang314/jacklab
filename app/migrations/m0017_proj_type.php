<?php

use app\core\Application;

class m0017_proj_type{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `proj_type`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '專案性質',
            PRIMARY KEY (`Id`) USING BTREE
          ) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `proj_type`;";
        $db->pdo->exec($sql);
    }

}