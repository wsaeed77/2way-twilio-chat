<?php

namespace Chattermax\API;

require_once __DIR__ . '/../../vendor/autoload.php'; // Include Composer's autoload file

use Chattermax\DB\ConversationManager;
use Chattermax\Config\Helper;

$fromNumber = Helper::formatPhoneNumber($_POST['From']);
$toNumber = isset($_POST['To']) ? Helper::formatPhoneNumber($_POST['To']) : '';
$messageBody = isset($_POST['Body']) ? $_POST['Body'] : '';
$direction = 'inbound'; // Default direction

$response = ['success' => false, 'message' => '','status'=>''];

if (empty($fromNumber) || empty($toNumber) || empty($messageBody)) {
    $response['message'] = 'Missing required fields.';
    $response['status']=400;
    echo json_encode($response);
    exit;
}

$manager = new ConversationManager();

try {
    $conversationId = $manager->insertConversationAndMessage($toNumber, $fromNumber, $messageBody, $direction);
    $response['success'] = true;
    $response['status'] = 200;
    $response['message'] = 'Conversation and message created successfully.';
} catch (Exception $e) {
    $response['message'] = 'Failed to create conversation and message: ' . $e->getMessage();
}

echo json_encode($response);
