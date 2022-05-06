<?php

use app\core\Application;

class m0009_game_record{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `game_record`  (
            `Id` int NOT NULL COMMENT 'PK',
            `Name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '主題名稱',
            `Game_group` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '參賽組別，可省略',
            `Ranking` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名次',
            `Game_time` date NOT NULL COMMENT '比賽日期',
            `Uploader` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FK, member.Account',
            `Game_type` int UNSIGNED NOT NULL COMMENT 'FK, game_type.Id',
            `Deleted` datetime NULL DEFAULT NULL COMMENT '軟刪除',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `game_record_uploader_foreign`(`Uploader`) USING BTREE,
            INDEX `game_record_game_type_foreign`(`Game_type`) USING BTREE,
            CONSTRAINT `game_record_game_type_foreign` FOREIGN KEY (`Game_type`) REFERENCES `game_type` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT,
            CONSTRAINT `game_record_uploader_foreign` FOREIGN KEY (`Uploader`) REFERENCES `member` (`Account`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `game_record`;";
        $db->pdo->exec($sql);
    }

}