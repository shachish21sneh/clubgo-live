<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
$uid = isset($data['uid']) ? (int)$data['uid'] : 0;
$cid = isset($data['cid']) ? (int)$data['cid'] : 0;

if ($cid <= 0) {
    echo json_encode(["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Category ID is required!"]);
    exit;
}

$eventlist = $event->query("SELECT id, title, img, cover_img, sdate, address FROM tbl_event WHERE status=1 AND FIND_IN_SET('$cid', cid) ORDER BY id DESC LIMIT 50");

$v = [];
if ($eventlist && $eventlist->num_rows > 0) {
    // Pre-fetch bookmarks
    $userBookmarks = [];
    if ($uid > 0) {
        $favRes = $event->query("SELECT eid FROM tbl_fav WHERE uid=$uid");
        if ($favRes) {
            while ($fr = $favRes->fetch_assoc()) {
                $userBookmarks[(int)$fr['eid']] = 1;
            }
        }
    }

    $eids = [];
    $rawEvents = [];
    while ($ev = $eventlist->fetch_assoc()) {
        $rawEvents[] = $ev;
        $eids[] = (int)$ev['id'];
    }

    $eidString = implode(',', $eids);
    $sponsorMap = [];
    if (!empty($eidString)) {
        $sRes = $event->query("SELECT id, eid, img, title FROM tbl_sponsore WHERE status=1 AND eid IN ($eidString) ORDER BY id ASC");
        if ($sRes) {
            while ($sr = $sRes->fetch_assoc()) {
                $eIdKey = (int)$sr['eid'];
                if (!isset($sponsorMap[$eIdKey])) {
                    $sponsorMap[$eIdKey] = [
                        'sponsore_id'    => $sr['id'],
                        'sponsore_img'   => $sr['img'],
                        'sponsore_title' => $sr['title']
                    ];
                }
            }
        }
    }

    foreach ($rawEvents as $ev) {
        $eid = (int)$ev['id'];
        $date = date_create($ev['sdate']);
        $v[] = [
            'event_id'      => (string)$eid,
            'event_title'   => $ev['title'],
            'event_img'     => $ev['cover_img'] ?: $ev['img'],
            'event_sdate'   => $date ? date_format($date, "d F") : '',
            'event_address' => $ev['address'] ?? '',
            'IS_BOOKMARK'   => isset($userBookmarks[$eid]) ? 1 : 0,
            'sponsore_list' => $sponsorMap[$eid] ?? (object)[]
        ];
    }
}

if (empty($v)) {
    echo json_encode(["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Search Data Not Get!!", "SearchData" => []]);
} else {
    echo json_encode(["ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Search Data Get Successfully!", "SearchData" => $v]);
}