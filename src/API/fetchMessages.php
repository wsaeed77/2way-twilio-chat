<?php

namespace Chattermax\API;

header('Content-Type: application/json');

require_once __DIR__ . '/../../vendor/autoload.php'; // Include Composer's autoload file

use Chattermax\DB\ConversationManager;

$conversationId = isset($_POST['conversationId']) ? $_POST['conversationId'] : '';

if ($conversationId) {
    $manager = new ConversationManager();
    $messages = $manager->getMessagesByConversationId($conversationId);
    echo json_encode($messages);
} else {
    echo json_encode(['error' => 'No conversation ID provided']);
}
