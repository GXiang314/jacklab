<?php

use app\core\Application;

class m0010_game_file{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `game_file`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '檔名',
            `Type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '類型',
            `Url` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '路徑',
            `Game_record` int NOT NULL COMMENT 'FK, game_record.Id',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `game_file_game_record_foreign`(`Game_record`) USING BTREE,
            CONSTRAINT `game_file_game_record_foreign` FOREIGN KEY (`Game_record`) REFERENCES `game_record` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `game_file`;";
        $db->pdo->exec($sql);
    }

}