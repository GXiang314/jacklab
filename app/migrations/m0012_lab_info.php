<?php

use app\core\Application;

class m0012_lab_info{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `lab_info`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Title` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '標題',
            `Content` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '內文',
            PRIMARY KEY (`Id`) USING BTREE
          ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `lab_info`;";
        $db->pdo->exec($sql);
    }

}