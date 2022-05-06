<?php

use app\core\Application;

class m0022_role{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `role`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '角色名稱',
            PRIMARY KEY (`Id`) USING BTREE
          ) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `role`;";
        $db->pdo->exec($sql);
    }

}