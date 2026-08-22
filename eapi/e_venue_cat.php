<?php 
require dirname(dirname(__FILE__)) . '/include/eventconfig.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$uid = isset($data['uid']) ? $data['uid'] : '';

if ($uid == '') {
    echo json_encode([
        "ResponseCode" => "401",
        "Result" => "false",
        "ResponseMsg" => "Something went wrong, try again!"
    ]);
    exit;
}

$v = [];

$query = "SELECT * FROM tbl_venue_category WHERE status = 'A'";

$venuecatlist = $event->query($query);

while ($cat = $venuecatlist->fetch_assoc()) {
    $v[] = $cat;
}

if (empty($v)) {
    echo json_encode([
        "ResponseCode" => "404",
        "Result" => "false",
        "ResponseMsg" => "Venue Category List Not Found!",
        "SearchData" => $v
    ]);
} else {
    echo json_encode([
        "ResponseCode" => "200",
        "Result" => "true",
        "ResponseMsg" => "Venue Category List Found",
        "SearchData" => $v
    ]);
}
?>
