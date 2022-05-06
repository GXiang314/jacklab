<?php

use app\core\Application;

class m0011_game_member{

    public function up()
    {
        $db = Application::$app->db;
        $sql = "
        CREATE TABLE `game_member`  (
            `Id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'PK',
            `Game_record` int NOT NULL COMMENT 'FK, game_record.Id',
            `Student_Id` int NOT NULL COMMENT 'FK, student.Id',
            PRIMARY KEY (`Id`) USING BTREE,
            INDEX `game_member_game_record_foreign`(`Game_record`) USING BTREE,
            INDEX `game_member_student_id_foreign`(`Student_Id`) USING BTREE,
            CONSTRAINT `game_member_game_record_foreign` FOREIGN KEY (`Game_record`) REFERENCES `game_record` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT,
            CONSTRAINT `game_member_student_id_foreign` FOREIGN KEY (`Student_Id`) REFERENCES `student` (`Id`) ON DELETE CASCADE ON UPDATE RESTRICT
          ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;
        ";
        $db->pdo->exec($sql);
    }
    
    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE IF EXISTS `game_member`;";
        $db->pdo->exec($sql);
    }

}