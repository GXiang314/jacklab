<?php

use app\core\Application;

class m0024_permission{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `permission`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '權限名稱',
            `Url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '路由',
            PRIMARY KEY (`Id`) USING BTREE
          ) ENGINE = InnoDB AUTO_INCREMENT = 37 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `permission`;";
        $db->pdo->exec($sql);
    }

}