<?php

namespace Chattermax\API;

require_once __DIR__ . '/../../vendor/autoload.php'; // Autoload dependencies

use Chattermax\DB\ConversationManager;
use Chattermax\Config\Helper;

// Helper function to return a structured response and exit
function sendResponse($success, $message, $statusCode) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'status'  => $statusCode
    ]);
    exit;
}

// Sanitize and format input
$fromNumber = Helper::formatPhoneNumber(trim($_POST['From'] ?? ''));
$toNumber = Helper::formatPhoneNumber(trim($_POST['To'] ?? ''));
$messageBody = trim($_POST['Body'] ?? '');
$direction = 'inbound'; // Default direction

// Check for required fields
if (empty($fromNumber) || empty($toNumber) || empty($messageBody)) {
    sendResponse(false, 'Missing required fields.', 400);
}

$manager = new ConversationManager();

try {
    // Insert conversation and message
    $conversationId = $manager->insertConversationAndMessage($toNumber, $fromNumber, $messageBody, $direction);

    // Send success response
    sendResponse(true, 'Conversation and message created successfully.', 200);

} catch (\Throwable $e) {  // Catch all types of errors (including exceptions and other critical issues)
    // Handle failure scenario with appropriate error message
    sendResponse(false, 'Failed to create conversation and message: ' . $e->getMessage(), 500);
}

