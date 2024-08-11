<?php

namespace Chattermax\API;

require_once __DIR__ . '/../../vendor/autoload.php'; // Include Composer's autoload file

use Chattermax\DB\ConversationManager;
use Chattermax\Config\Config;

$fromNumber = Config::get('TWILIO_NUMBER');

$manager = new ConversationManager();
$conversations = $manager->getAllConversations($fromNumber);

echo json_encode($conversations);
