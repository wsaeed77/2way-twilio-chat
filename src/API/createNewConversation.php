<?php

namespace Chattermax\API;

use Chattermax\DB\ConversationManager;
use Chattermax\Config\Config;

// Assuming POST request
$fromNumber =  Config::get('TWILIO_NUMBER');
$toNumber = isset($_POST['toNumber']) ? $_POST['toNumber'] : '';
$messageBody = isset($_POST['messageBody']) ? $_POST['messageBody'] : '';
$direction = isset($_POST['direction']) ? $_POST['direction'] : 'outbound'; // Default direction

$response = ['success' => false, 'message' => '', 'conversationId' => null];

if (empty($fromNumber) || empty($toNumber) || empty($messageBody)) {
    $response['message'] = 'Missing required fields.';
    echo json_encode($response);
    exit;
}

$manager = new ConversationManager();

try {
    $conversationId = $manager->insertConversationAndMessage($fromNumber, $toNumber, $messageBody, $direction);
    $response['success'] = true;
    $response['message'] = 'Conversation and message created successfully.';
    $response['conversationId'] = $conversationId; // Include the conversation ID in the response
} catch (Exception $e) {
    $response['message'] = 'Failed to create conversation and message: ' . $e->getMessage();
}

echo json_encode($response);
