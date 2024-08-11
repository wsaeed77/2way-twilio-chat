<?php

namespace Chattermax\API;

require_once __DIR__ . '/../../vendor/autoload.php'; // Include Composer's autoload file

use Chattermax\DB\ConversationManager;
use Chattermax\Config\Config;
use Twilio\Rest\Client;

// Create an instance of ConversationManager
$manager = new ConversationManager();

// Define the parameters
$conversationId = isset($_POST['conversationId']) ? $_POST['conversationId'] : '';
$messageBody = isset($_POST['messageBody']) ? $_POST['messageBody'] : '';
$direction = "outbound"; // Can be 'inbound' or 'outbound'

// Twilio credentials
$accountSid =  Config::get('TWILIO_ACCOUNT_SID');
$authToken = Config::get('TWILIO_AUTH_TOKEN');
$twilioNumber = Config::get('TWILIO_NUMBER');;

// Fetch the recipient number from the conversation
$conversation = $manager->getConversationById($conversationId);
$toNumber = isset($conversation['to_number']) ? $conversation['to_number'] : '';

$smsSid = null;

if (!empty($accountSid) && !empty($authToken)) {
    // Send the SMS using Twilio
    $client = new Client($accountSid, $authToken);
    $message = $client->messages->create(
        $toNumber,
        [
            'from' => $twilioNumber,
            'body' => $messageBody
        ]
    );

    // Get the SMS SID
    $smsSid = $message->sid;
}

// Insert the message along with the SMS SID
$manager->insertMessage($conversationId, $messageBody, $direction, $smsSid);

// Implement any additional logic needed after insertion
echo json_encode(['success' => true, 'message' => 'Message created successfully.']);
?>
