<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require dirname(dirname(__FILE__)) . '/include/eventconfig.php';

header('Content-Type: application/json; charset=utf-8');

// Read JSON input safely
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

// Sanitize inputs
$uid   = isset($data['uid'])   ? (int)$data['uid'] : 0;
$lats  = isset($data['lats'])  ? (float)$data['lats'] : 0.0;
$longs = isset($data['longs']) ? (float)$data['longs'] : 0.0;

if (empty($uid)) {
    echo json_encode([
        "ResponseCode" => "401",
        "Result"       => "false",
        "ResponseMsg"  => "Something went wrong, try again!"
    ]);
    exit;
}

// 1. Fetch categories
$cp = [];
$cato = $event->query("SELECT id, title, img, cover_img FROM tbl_cat WHERE status=1 ORDER BY id ASC");
if ($cato) {
    while ($row = $cato->fetch_assoc()) {
        $cp[] = [
            'id'        => $row['id'],
            'title'     => $row['title'],
            'cat_img'   => $row['img'],
            'cover_img' => $row['cover_img']
        ];
    }
}

$timestamp = date("Y-m-d");
$chtime    = date("Y-m-d H:i:s");

// 2. Pre-fetch User Bookmarks into memory map O(1) lookup
$userBookmarks = [];
if ($uid > 0) {
    $favRes = $event->query("SELECT eid FROM tbl_fav WHERE uid=$uid");
    if ($favRes) {
        while ($fr = $favRes->fetch_assoc()) {
            $userBookmarks[(int)$fr['eid']] = 1;
        }
    }
}

// 3. Pre-fetch Ticket Booking Totals into memory map O(1) lookup
$eventTicketsMap = [];
$tRes = $event->query("SELECT eid, SUM(ticket_book) AS books FROM tbl_type_price GROUP BY eid");
if ($tRes) {
    while ($tr = $tRes->fetch_assoc()) {
        $eventTicketsMap[(int)$tr['eid']] = (string)($tr['books'] ?? 0);
    }
}

// 4. Pre-fetch Sponsors into memory map O(1) lookup
$sponsorMap = [];
$sRes = $event->query("SELECT id, eid, img, title FROM tbl_sponsore WHERE status=1 ORDER BY id ASC");
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

// 5. Pre-fetch Member Avatars in 1 single fast indexed JOIN
$membersMap = [];
$mRes = $event->query("SELECT t.eid, u.pro_pic FROM tbl_ticket t JOIN tbl_user u ON t.uid=u.id WHERE u.pro_pic IS NOT NULL AND u.pro_pic != '' GROUP BY t.eid, t.uid");
if ($mRes) {
    while ($mr = $mRes->fetch_assoc()) {
        $eIdKey = (int)$mr['eid'];
        if (!isset($membersMap[$eIdKey])) {
            $membersMap[$eIdKey] = [];
        }
        if (count($membersMap[$eIdKey]) < 5) {
            $membersMap[$eIdKey][] = $mr['pro_pic'];
        }
    }
}

// In-memory cache for processed events
$eventCache = [];

function getFastEventData($ev, $userBookmarks, $sponsorMap, $membersMap, $eventTicketsMap) {
    global $eventCache;
    $eid = (int)$ev['id'];
    if (isset($eventCache[$eid])) {
        return $eventCache[$eid];
    }

    $date = date_create($ev['sdate']);
    $data = [
        'event_id'          => (string)$eid,
        'event_title'       => $ev['title'],
        'event_img'         => $ev['cover_img'] ?: $ev['img'],
        'is_booked'         => $ev['is_booked'] ?? 0,
        'event_sdate'       => $date ? date_format($date, "d F") : '',
        'event_address'     => $ev['address'] ?? '',
        'IS_BOOKMARK'       => isset($userBookmarks[$eid]) ? 1 : 0,
        'sponsore_list'     => $sponsorMap[$eid] ?? [],
        'member_list'       => $membersMap[$eid] ?? [],
        'total_member_list' => $eventTicketsMap[$eid] ?? "0"
    ];

    $eventCache[$eid] = $data;
    return $data;
}

// 6. Upcoming events (Pending)
$v = [];
$eventlist = $event->query("SELECT id, title, img, cover_img, sdate, address, is_booked FROM tbl_event WHERE status=1 AND event_status='Pending' ORDER BY sdate ASC LIMIT 20");
if ($eventlist) {
    while ($ev = $eventlist->fetch_assoc()) {
        $v[] = getFastEventData($ev, $userBookmarks, $sponsorMap, $membersMap, $eventTicketsMap);
    }
}

// 7. Trending events (Today / Recent)
$sec = [];
$eventlistc = $event->query("SELECT id, title, img, cover_img, sdate, address, is_booked FROM tbl_event WHERE status=1 ORDER BY id DESC LIMIT 5");
if ($eventlistc) {
    while ($evc = $eventlistc->fetch_assoc()) {
        $sec[] = getFastEventData($evc, $userBookmarks, $sponsorMap, $membersMap, $eventTicketsMap);
    }
}

// 8. Nearby events
$d = [];
if ($lats != 0.0 && $longs != 0.0) {
    $eventlists = $event->query("
        SELECT (((ACOS(SIN(($lats*PI()/180)) * SIN((latitude*PI()/180))
        + COS(($lats*PI()/180)) * COS((latitude*PI()/180))
        * COS((($longs-longtitude)*PI()/180))))*180/PI())*60*1.1515*1.609344) AS distance,
        id, title, img, address, sdate, stime, is_booked, etime, cover_img
        FROM tbl_event
        WHERE status=1 ORDER BY distance ASC LIMIT 10
    ");
    if ($eventlists) {
        while ($evs = $eventlists->fetch_assoc()) {
            $data = getFastEventData($evs, $userBookmarks, $sponsorMap, $membersMap, $eventTicketsMap);
            $date = date_create($evs['sdate']);
            $data['event_sdate'] = strtoupper(($date ? date_format($date, "dS M - D - ") : '') . date("g:i A", strtotime($evs['stime'])));
            $data['event_img']   = $evs['img'];
            $d[] = $data;
        }
    }
}

// 9. This month events
$pop = [];
$month = date("m");
$year = date("Y");
$eve = $event->query("SELECT id, title, img, cover_img, sdate, address, is_booked FROM tbl_event WHERE status=1 AND MONTH(sdate) = $month AND YEAR(sdate) = $year ORDER BY sdate ASC LIMIT 15");
if ($eve) {
    while ($e = $eve->fetch_assoc()) {
        $pop[] = getFastEventData($e, $userBookmarks, $sponsorMap, $membersMap, $eventTicketsMap);
    }
}

// 10. User Wallet
$wallet = 0;
if ($uid > 0) {
    $tbwallet = $event->query("SELECT wallet FROM tbl_user WHERE id=$uid LIMIT 1")->fetch_assoc();
    $wallet = $tbwallet['wallet'] ?? 0;
}

// 11. Settings
$main_data = $event->query("SELECT id, currency, one_key, one_hash, scredit, rcredit FROM tbl_setting LIMIT 1")->fetch_assoc();
$pols = [
    'id'       => $main_data['id'] ?? '1',
    'currency' => $main_data['currency'] ?? '₹',
    'one_key'  => $main_data['one_key'] ?? '',
    'one_hash' => $main_data['one_hash'] ?? '',
    'scredit'  => $main_data['scredit'] ?? '0',
    'rcredit'  => $main_data['rcredit'] ?? '0'
];

// Final Response
$kp = [
    'Catlist'          => $cp,
    'Main_Data'        => $pols,
    'trending_event'   => $sec,
    'wallet'           => $wallet,
    'upcoming_event'   => $v,
    'nearby_event'     => $d,
    'this_month_event' => $pop
];

$returnArr = [
    "ResponseCode" => "200",
    "Result"       => "true",
    "ResponseMsg"  => "Home Data Get Successfully!",
    "HomeData"     => $kp
];

echo json_encode($returnArr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
