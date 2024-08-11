<?php

namespace Chattermax\API;

require_once __DIR__ . '/../../vendor/autoload.php'; // Include Composer's autoload file

use Chattermax\DB\ConversationManager;
use Chattermax\Config\Config;

$fromNumber = Config::TWILIO_NUMBER;

// Assuming you're using GET for simplicity; switch to POST if needed
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Instantiate the ConversationManager
$manager = new ConversationManager();

// Search for conversations
$results = $manager->searchConversations($query, $fromNumber);

// Return the results as JSON
header('Content-Type: application/json');
echo json_encode($results);
