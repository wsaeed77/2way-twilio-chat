<?php

session_start(); // Start the session at the beginning

// Check if the user is not logged in by checking a session variable (e.g., user_id)
if (!isset($_SESSION['user_id'])) {
    // Redirect to login.php if the user_id session variable is not set
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/vendor/autoload.php'; // Include Composer's autoload file

use Chattermax\Config\Config;
use Chattermax\DB\Database;
use Chattermax\DB\DBMigration;

// Create an instance of the Database class
$database = new Database();

// Function to run migrations if tables are not found
function runMigrationsIfNeeded($database) {
    if (!$database->tableExists('conversations')) {
        DBMigration::migrate();
    }
}

try {
    // Check if the necessary table exists and run migrations if it does not
    runMigrationsIfNeeded($database);
} catch (Exception $e) {
    echo "Failed to check or run migrations: " . $e->getMessage();
    exit;
}

include __DIR__ . '/includes/__header.php';
?>
<div class="row m-0">
    <div class="col-3 m-0 p-0">
        <?php include __DIR__ . '/includes/__sidebar.php'; ?>
    </div>
    <div class="col-9 m-0 p-0 position-relative container-bgk">
        <?php include __DIR__ . '/includes/__navbar.php'; ?>
        <?php include __DIR__ . '/includes/__messages.php'; ?>
        <?php include __DIR__ . '/includes/__chatbar.php'; ?>
    </div>
</div>
<?php include __DIR__ . '/includes/__footer.php'; ?>
