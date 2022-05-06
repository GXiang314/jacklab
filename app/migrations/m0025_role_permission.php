<?php

use app\core\Application;

class m0025_role_permission{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `role_permission`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Role_Id` int UNSIGNED NOT NULL COMMENT 'FK, role.Id',
            `Permission_Id` int UNSIGNED NOT NULL COMMENT 'FK, permission.Id',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `role_permission_role_id_foreign`(`Role_Id`) USING BTREE,
            INDEX `role_permission_permission_id_foreign`(`Permission_Id`) USING BTREE,
            CONSTRAINT `role_permission_permission_id_foreign` FOREIGN KEY (`Permission_Id`) REFERENCES `permission` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT,
            CONSTRAINT `role_permission_role_id_foreign` FOREIGN KEY (`Role_Id`) REFERENCES `role` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB AUTO_INCREMENT = 37 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `role_permission`;";
        $db->pdo->exec($sql);
    }

}