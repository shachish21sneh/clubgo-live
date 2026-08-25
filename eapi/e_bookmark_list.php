<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
$uid = isset($data['uid']) ? (int)$data['uid'] : 0;

if ($uid <= 0) {
    echo json_encode(["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "User ID is required!"]);
    exit;
}

$query = "SELECT e.id AS event_id, e.title AS event_title, COALESCE(NULLIF(e.cover_img, ''), e.img) AS event_img, e.sdate, e.address 
          FROM tbl_fav f 
          JOIN tbl_event e ON f.eid = e.id 
          WHERE f.uid = $uid AND e.status = 1 
          ORDER BY f.id DESC";

$result = $event->query($query);
$v = [];
if ($result && $result->num_rows > 0) {
    while ($ev = $result->fetch_assoc()) {
        $date = date_create($ev['sdate']);
        $v[] = [
            'event_id'      => (string)$ev['event_id'],
            'event_title'   => $ev['event_title'],
            'event_img'     => $ev['event_img'],
            'event_sdate'   => $date ? date_format($date, "d F") : '',
            'event_address' => $ev['address'] ?? ''
        ];
    }
    echo json_encode(["ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Bookmark List Get Successfully!", "EventData" => $v]);
} else {
    echo json_encode(["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Event Bookmark List Not Found!"]);
}