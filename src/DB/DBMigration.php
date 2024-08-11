<?php

namespace Chattermax\DB;

use PDO;
use PDOException;
use Chattermax\Config\Config;

class DBMigration {
    public static function migrate() {
        try {
            Config::init();
            // Create a new PDO instance
            $pdo = new PDO("mysql:host=" . Config::get('DB_HOST') . ";dbname=" . Config::get('DB_NAME'), Config::get('DB_USER'), Config::get('DB_PASS'));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Start the transaction
            $pdo->beginTransaction();

            // Check and create tables
            self::createTableIfNotExists($pdo, 'conversations', "
                CREATE TABLE `conversations` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `from_number` varchar(15) NOT NULL,
                    `to_number` varchar(15) NOT NULL,
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `last_message_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `from_number` (`from_number`,`to_number`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            self::createTableIfNotExists($pdo, 'messages', "
                CREATE TABLE `messages` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `conversation_id` int(11) NOT NULL,
                    `message_body` text NOT NULL,
                    `message_sid` varchar(100) DEFAULT NULL,
                    `direction` enum('inbound','outbound') NOT NULL,
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `is_read` tinyint(1) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`),
                    KEY `conversation_id` (`conversation_id`),
                    CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            self::createTableIfNotExists($pdo, 'phonenumbers', "
                CREATE TABLE `phonenumbers` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) DEFAULT NULL,
                    `number` varchar(255) NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            self::createTableIfNotExists($pdo, 'users', "
                CREATE TABLE `users` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `email` varchar(255) NOT NULL,
                    `password` varchar(255) NOT NULL,
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `email` (`email`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            // Insert a default user if users table is created
            if (self::tableExists($pdo, 'users')) {
                $password = 'VahcDGNpBZMe';
                $hashedPassword = md5($password);
                $pdo->exec("INSERT INTO users (email, password) VALUES ('admin@test.com', '$hashedPassword')");
            }

            // Commit the transaction
            $pdo->commit();

            echo "Database generated successfully";

        } catch (Exception $e) {
            // Rollback the transaction if something goes wrong
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo "Failed to create tables or add user: " . $e->getMessage();
        }
    }

    private static function createTableIfNotExists($pdo, $tableName, $createSQL) {
        if (!self::tableExists($pdo, $tableName)) {
            $pdo->exec($createSQL);
        }
    }

    private static function tableExists($pdo, $tableName) {
        try {
            $result = $pdo->query("SELECT 1 FROM $tableName LIMIT 1");
        } catch (PDOException $e) {
            return false;
        }
        return $result !== false;
    }
}
