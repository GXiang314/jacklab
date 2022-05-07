<?php

use app\core\Application;

class m0006_book{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE if not exists `book`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '書名',
            `Publisher` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '出版社',
            `Time` date NOT NULL COMMENT '出版時間',
            `ISBN` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ISBN',
            `Image` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '圖片路徑',
            PRIMARY KEY (`Id`) USING BTREE
          ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `book`;";
        $db->pdo->exec($sql);
    }

}