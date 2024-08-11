<?php

namespace Chattermax\DB;

require_once __DIR__ . '/../../vendor/autoload.php'; // Include Composer's autoload file

use PDO;

class UserManager extends Database
{
    public function getUser($email, $password)
    {
        $encrypted_password = md5($password);
        $query = "SELECT * FROM users WHERE email = :email AND password = :password";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $encrypted_password);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
