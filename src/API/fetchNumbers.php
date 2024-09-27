<?php

namespace Chattermax\API;

require_once __DIR__ . '/../../vendor/autoload.php'; 

use Chattermax\DB\PhoneNumberManager;

$manager = new PhoneNumberManager();
$numbers = $manager->getAllNumbers();

echo json_encode($numbers);
