<?php

namespace Chattermax\API;

require_once __DIR__ . '/../../vendor/autoload.php'; // Include Composer's autoload file

use Chattermax\DB\ConversationManager;

$conversationManager = new ConversationManager(); // Initialize your ConversationManager

// Check if message ID is provided
if (isset($_POST['messageId'])) {
    $messageId = $_POST['messageId'];
    $result = $conversationManager->markMessageAsRead($messageId);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to mark message as read.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No message ID provided.']);
}
