<?php

use app\core\Application;

class m0024_permission_group{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `permission_group`  (
            `Id` varchar(20) NOT NULL COMMENT 'PK',
            `Name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '群組名稱',
            PRIMARY KEY (`Id`) USING BTREE
          ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `permission_group`;";
        $db->pdo->exec($sql);
    }

}