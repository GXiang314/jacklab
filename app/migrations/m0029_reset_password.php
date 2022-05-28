<?php

use app\core\Application;

class m0029_reset_password{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `reset_password`  (
            `Id` int NOT NULL AUTO_INCREMENT,
            `Account` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL,
            `Update_at` datetime NOT NULL,
            `Code` char(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_as_ci NOT NULL,
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `foreign_reset_member`(`Account`) USING BTREE
          ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_as_ci ROW_FORMAT = Dynamic;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `reset_password`;";
        $db->pdo->exec($sql);
    }

}