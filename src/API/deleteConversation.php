<?php

namespace Chattermax\API;

require_once __DIR__ . '/../../vendor/autoload.php'; // Include Composer's autoload file

use Chattermax\DB\ConversationManager;

// Define the parameters
$conversationId = isset($_POST['conversationId']) ? $_POST['conversationId'] : '';

$manager = new ConversationManager();

$manager->deleteConversation($conversationId);

$response = ['success' => true, 'message' => 'Conversation deleted successfully.'];

echo json_encode($response);
