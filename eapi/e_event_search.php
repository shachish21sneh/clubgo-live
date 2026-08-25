<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
$uid = isset($data['uid']) ? (int)$data['uid'] : 0;
$title = isset($data['title']) ? trim($data['title']) : '';

if (empty($title)) {
    echo json_encode(["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Search query is required!"]);
    exit;
}

$escaped = $event->real_escape_string($title);
$eventlist = $event->query("SELECT id, title, img, cover_img, sdate, address FROM tbl_event WHERE status=1 AND title LIKE '%$escaped%' ORDER BY id DESC LIMIT 50");

$v = [];
if ($eventlist && $eventlist->num_rows > 0) {
    // Pre-fetch bookmarks for this user
    $userBookmarks = [];
    if ($uid > 0) {
        $favRes = $event->query("SELECT eid FROM tbl_fav WHERE uid=$uid");
        if ($favRes) {
            while ($fr = $favRes->fetch_assoc()) {
                $userBookmarks[(int)$fr['eid']] = 1;
            }
        }
    }

    // Collect event IDs
    $eids = [];
    $rawEvents = [];
    while ($ev = $eventlist->fetch_assoc()) {
        $rawEvents[] = $ev;
        $eids[] = (int)$ev['id'];
    }

    $eidString = implode(',', $eids);

    // Pre-fetch Sponsors
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

        // Pre-fetch member avatars
        $membersMap = [];
        $mRes = $event->query("SELECT t.eid, u.pro_pic FROM tbl_ticket t JOIN tbl_user u ON t.uid=u.id WHERE t.eid IN ($eidString) AND u.pro_pic IS NOT NULL AND u.pro_pic != '' GROUP BY t.eid, t.uid");
        if ($mRes) {
            while ($mr = $mRes->fetch_assoc()) {
                $eIdKey = (int)$mr['eid'];
                if (!isset($membersMap[$eIdKey])) $membersMap[$eIdKey] = [];
                if (count($membersMap[$eIdKey]) < 5) $membersMap[$eIdKey][] = $mr['pro_pic'];
            }
        }

        // Pre-fetch ticket counts
        $ticketsMap = [];
        $tRes = $event->query("SELECT eid, SUM(ticket_book) AS books FROM tbl_type_price WHERE eid IN ($eidString) GROUP BY eid");
        if ($tRes) {
            while ($tr = $tRes->fetch_assoc()) {
                $ticketsMap[(int)$tr['eid']] = (string)($tr['books'] ?? 0);
            }
        }
    }

    foreach ($rawEvents as $ev) {
        $eid = (int)$ev['id'];
        $date = date_create($ev['sdate']);
        $v[] = [
            'event_id'          => (string)$eid,
            'event_title'       => $ev['title'],
            'event_img'         => $ev['cover_img'] ?: $ev['img'],
            'event_sdate'       => $date ? date_format($date, "d F") : '',
            'event_address'     => $ev['address'] ?? '',
            'IS_BOOKMARK'       => isset($userBookmarks[$eid]) ? 1 : 0,
            'sponsore_list'     => $sponsorMap[$eid] ?? (object)[],
            'member_list'       => $membersMap[$eid] ?? [],
            'total_member_list' => $ticketsMap[$eid] ?? "0"
        ];
    }
}

if (empty($v)) {
    echo json_encode(["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Search Data Not Get!!", "SearchData" => []]);
} else {
    echo json_encode(["ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Search Data Get Successfully!", "SearchData" => $v]);
}