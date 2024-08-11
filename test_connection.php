<?php

require_once __DIR__ . '/vendor/autoload.php';

use Chattermax\Config\Config;


Config::init();

try {
    $pdo = new PDO("mysql:host=" . Config::get('DB_HOST') . ";dbname=" . Config::get('DB_NAME'), Config::get('DB_USER'), Config::get('DB_PASS'));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to database successfully.";
} catch (PDOException $exception) {
    echo "Connection error: " . $exception->getMessage();
}
