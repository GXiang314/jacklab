<?php

use app\core\Application;

class m0023_member_role{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE if not exists `member_role`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Account` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK, member.Account',
            `Role_Id` int UNSIGNED NOT NULL COMMENT 'FK, role.Id',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `member_role_account_foreign`(`Account`) USING BTREE,
            INDEX `member_role_role_id_foreign`(`Role_Id`) USING BTREE,
            CONSTRAINT `member_role_account_foreign` FOREIGN KEY (`Account`) REFERENCES `member` (`Account`) ON DELETE CASCADE ON UPDATE RESTRICT,
            CONSTRAINT `member_role_role_id_foreign` FOREIGN KEY (`Role_Id`) REFERENCES `role` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `member_role`;";
        $db->pdo->exec($sql);
    }

}