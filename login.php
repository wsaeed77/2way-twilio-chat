<?php

session_start(); // Ensure the session is started

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

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to the homepage or dashboard
    header('Location: index.php');
    exit;
}

include_once __DIR__ . '/includes/__header.php'; // Ensure the path is correct
?>
<div class="login-container">
    <div class="bg-light py-3 py-md-5">
        <div class="container">
            <div class="row justify-content-md-center">
                <div class="col-12 col-md-11 col-lg-8 col-xl-7 col-xxl-6">
                    <div class="bg-white p-4 p-md-5 rounded shadow-sm">
                        <!-- Display error message if it exists -->
                        <?php if (isset($_GET['error'])): ?>
                            <div id="errorAlert" class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($_GET['error']); ?>
                            </div>
                            <script>
                                setTimeout(function() {
                                    document.getElementById('errorAlert').style.display = 'none';
                                }, 1500);
                            </script>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-5">
                                    <h3>Log in</h3>
                                </div>
                            </div>
                        </div>
                        <form action="login_process.php" method="post">
                            <div class="row gy-3 gy-md-4 overflow-hidden">
                                <div class="col-12">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required>
                                </div>
                                <div class="col-12">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" id="password" required>
                                </div>
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button class="btn btn-lg btn-primary" type="submit">Log in now</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once __DIR__ . '/includes/__footer.php'; ?> // Ensure the path is correct
</div>
