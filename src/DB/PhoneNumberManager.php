<?php

namespace Chattermax\DB;

require_once __DIR__ . '/../../vendor/autoload.php'; // Include Composer's autoload file

use PDO;

class PhoneNumberManager extends Database {
    public function getAllNumbers() {
        $query = "SELECT * FROM phonenumbers";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
