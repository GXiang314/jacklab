<?php

use app\core\Application;

class m0001_member{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `member`  (
            `Account` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'PK, 電子信箱',
            `Password` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密碼',
            `AuthToken` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '信箱驗證Token',
            `CreateTime` datetime NOT NULL COMMENT '創建帳號時間',
            `IsAdmin` bit(1) NOT NULL COMMENT 'Admin',
            PRIMARY KEY (`Account`) USING BTREE
          ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `member`;";
        $db->pdo->exec($sql);
    }

}