<?php

use app\core\Application;

class m0019_proj_record{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE if not exists `proj_record`  (
            `Id` int NOT NULL COMMENT 'PK',
            `Remark` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '備註',
            `CreateTime` datetime NOT NULL COMMENT '上傳時間',
            `Uploader` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK, member.Account',
            `Project_Id` int UNSIGNED NOT NULL COMMENT 'FK, project.Id',
            `Deleted` datetime NULL DEFAULT NULL COMMENT '軟刪除',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `proj_record_uploader_foreign`(`Uploader`) USING BTREE,
            INDEX `proj_record_project_id_foreign`(`Project_Id`) USING BTREE,
            CONSTRAINT `proj_record_project_id_foreign` FOREIGN KEY (`Project_Id`) REFERENCES `project` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT,
            CONSTRAINT `proj_record_uploader_foreign` FOREIGN KEY (`Uploader`) REFERENCES `member` (`Account`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `proj_record`;";
        $db->pdo->exec($sql);
    }

}