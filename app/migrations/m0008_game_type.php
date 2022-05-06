<?php

use app\core\Application;

class m0008_game_type{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `game_type`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '比賽性質',
            PRIMARY KEY (`Id`) USING BTREE
          ) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `game_type`;";
        $db->pdo->exec($sql);
    }

}