<?php

use app\core\Application;

class m0007_author{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE if not exists `author`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Book_Id` int UNSIGNED NOT NULL COMMENT 'FK, books.Id',
            `Account` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK, member.Account',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `authors_account_foreign`(`Account`) USING BTREE,
            INDEX `authors_book_id_foreign`(`Book_Id`) USING BTREE,
            CONSTRAINT `authors_account_foreign` FOREIGN KEY (`Account`) REFERENCES `member` (`Account`) ON DELETE CASCADE ON UPDATE RESTRICT,
            CONSTRAINT `authors_book_id_foreign` FOREIGN KEY (`Book_Id`) REFERENCES `book` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `author`;";
        $db->pdo->exec($sql);
    }

}