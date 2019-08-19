<?php

require 'vendor/autoload.php';

use App\DbConnect as DB;
use App\Helpers\EquipmentAvailabilityHelper;
use App\Helpers\ValidateRequestHelper;

header('Content-Type: application/json');

try {
    // Getting database connection
    $db = (new DB())->connect();

    // Validating user input
    (new ValidateRequestHelper($_REQUEST))->validate();

    // Passing connection as a dependency
    $equipmentAvailabilityHelper = new EquipmentAvailabilityHelper($db);

    // Casting data
    $start = new DateTime($_REQUEST['start']);
    $end = new DateTime($_REQUEST['end']);

    switch ($_REQUEST['action']) {
        case 'isAvailable':
        $equipmentId = (int)$_REQUEST['equipmentId'];
        $quantity = (int)$_REQUEST['quantity'];
        $result = $equipmentAvailabilityHelper->isAvailable($equipmentId, $quantity, $start, $end);
            break;
        case 'getShortages':
        $result = $equipmentAvailabilityHelper->getShortages($start, $end);
            break;
    }

} catch (Exception $e) {
    die(json_encode([
        'error' => [
            [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]
        ]
    ]));
}

echo json_encode(['result' => $result]);



