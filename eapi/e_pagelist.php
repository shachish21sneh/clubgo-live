<?php
require dirname(dirname(__FILE__)) . '/include/eventconfig.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// optional input
$data = json_decode(file_get_contents('php://input'), true);

$c = array();

// Run query
$sel = $event->query("SELECT * FROM tbl_page WHERE status=1");
if (!$sel) {
    echo json_encode([
        "pagelist" => [],
        "ResponseCode" => "500",
        "Result" => "false",
        "ResponseMsg" => "Database query failed: " . $event->error
    ]);
    exit;
}

// UTF-8 safe function
function safe($str) {
    return mb_convert_encoding($str, 'UTF-8', 'UTF-8');
}

// Build response
while ($row = $sel->fetch_assoc()) {
    $c[] = [
        "title"       => safe($row['title']),
        "description" => safe($row['description']) // can contain HTML safely
    ];
}

// Final array
if (empty($c)) {
    $returnArr = [
        "pagelist" => $c,
        "ResponseCode" => "200",
        "Result" => "false",
        "ResponseMsg" => "Pages Not Found!"
    ];
} else {
    $returnArr = [
        "pagelist" => $c,
        "ResponseCode" => "200",
        "Result" => "true",
        "ResponseMsg" => "Pages List Found!"
    ];
}

// Encode JSON with safe flags
$json = json_encode($returnArr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// If encoding fails, show error
if ($json === false) {
    echo json_encode([
        "pagelist" => [],
        "ResponseCode" => "500",
        "Result" => "false",
        "ResponseMsg" => "JSON encoding error: " . json_last_error_msg()
    ]);
    exit;
}

echo $json;
?>