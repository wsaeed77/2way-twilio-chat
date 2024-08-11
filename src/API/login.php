<?php

namespace Chattermax\API;

session_start();

require_once __DIR__ . '/../../vendor/autoload.php'; // Include Composer's autoload file

use Chattermax\DB\UserManager;

$error = ''; // Initialize error message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        $userManager = new UserManager();
        $user = $userManager->getUser($email, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: ../index.php');
            exit;
        } else {
            $error = 'Incorrect email or password';
        }
    }
}

if ($error) {
    // Redirect back to the login page with an error message
    header('Location: /login.php?error=' . urlencode($error));
    exit;
}
