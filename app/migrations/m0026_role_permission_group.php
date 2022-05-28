<?php

use app\core\Application;

class m0026_role_permission_group{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE if not exists `role_permission_group`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Role_Id` int UNSIGNED NOT NULL COMMENT 'FK, role.Id',
            `Permission_group` varchar(20) NOT NULL COMMENT 'FK, permission_group.Id',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `role_permission_role_id_foreign`(`Role_Id`) USING BTREE,
            INDEX `role_permission_group_permission_group_foreign`(`Permission_group`) USING BTREE,
            CONSTRAINT `role_permission_role_id_foreign` FOREIGN KEY (`Role_Id`) REFERENCES `role` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB AUTO_INCREMENT = 37 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        /**
        * CONSTRAINT `role_permission_group_permission_group_foreign` FOREIGN KEY (`Permission_group`) REFERENCES `permission_group` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT,

        */
        
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `role_permission_group`;";
        $db->pdo->exec($sql);
    }

}