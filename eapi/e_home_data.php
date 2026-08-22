<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);

require dirname(dirname(__FILE__)) . '/include/eventconfig.php';

header('Content-Type: application/json; charset=utf-8');

// Read JSON input safely
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

// Sanitize inputs
$uid   = isset($data['uid'])   ? (int)$data['uid'] : 0;
$lats  = isset($data['lats'])  ? trim($data['lats']) : '';
$longs = isset($data['longs']) ? trim($data['longs']) : '';

if (empty($uid) || empty($lats) || empty($longs)) {
    $returnArr = [
        "ResponseCode" => "401",
        "Result"       => "false",
        "ResponseMsg"  => "Something went wrong, try again!"
    ];
    echo json_encode($returnArr);
    exit;
}

// Initialize arrays
$v   = [];
$cp  = [];
$d   = [];
$pop = [];
$sec = [];

// Fetch categories
$cato = $event->query("SELECT * FROM tbl_cat WHERE status=1");
while ($row = $cato->fetch_assoc()) {
    $cp[] = [
        'id'        => $row['id'],
        'title'     => $row['title'],
        'cat_img'   => $row['img'],
        'cover_img' => $row['cover_img']
    ];
}

$timestamp = date("Y-m-d");
$chtime    = date("Y-m-d H:i:s");

/**
 * Helper: Get event sponsor list (returns only first sponsor or empty array)
 */
function getFirstSponsor($event, $eid) {
    $spon = $event->query("SELECT * FROM tbl_sponsore WHERE eid=$eid AND status=1");
    $sponsors = [];
    while ($row = $spon->fetch_assoc()) {
        $sponsors[] = [
            'sponsore_id'    => $row['id'],
            'sponsore_img'   => $row['img'],
            'sponsore_title' => $row['title']
        ];
    }
    return $sponsors[0] ?? [];
}

/**
 * Helper: Get member profile pics
 */
function getEventMembers($event, $eid) {
    $ulist = $event->query("SELECT uid FROM tbl_ticket WHERE eid=$eid GROUP BY uid");
    $members = [];
    while ($rp = $ulist->fetch_assoc()) {
        $getpic = $event->query("SELECT pro_pic FROM tbl_user WHERE id=" . (int)$rp['uid'])->fetch_assoc();
        if (!empty($getpic['pro_pic'])) {
            $members[] = $getpic['pro_pic'];
        }
    }
    return $members;
}

/**
 * Helper: Get total booked tickets
 */
function getTotalTickets($event, $eid) {
    $ticket = $event->query("SELECT SUM(ticket_book) AS books FROM tbl_type_price WHERE eid=$eid")->fetch_assoc();
    return $ticket['books'] ?? 0;
}

/**
 * Helper: Build event data array
 */
function buildEventData($event, $ev, $uid, $timestamp, $chtime) {
    $eid = (int)$ev['id'];

    // Mark events completed/booked if past date/time
    if ($ev['sdate'] <= $timestamp && ($ev['sdate'] . ' ' . $ev['etime']) <= $chtime) {
        $event->query("UPDATE tbl_event SET event_status='Completed' WHERE id=$eid");
        $event->query("UPDATE tbl_ticket SET ticket_type='Completed' WHERE eid=$eid AND ticket_type='Booked'");
        // return null;
    } else {
        $event->query("UPDATE tbl_event SET is_booked=1 WHERE id=$eid");
    }

    $date = date_create($ev['sdate']);

    return [
        'event_id'         => (string)$eid,
        'event_title'      => $ev['title'],
        'event_img'        => $ev['cover_img'] ?? $ev['img'], // fallback
        'is_booked'        => $ev['is_booked'],
        'event_sdate'      => date_format($date, "d F"),
        'event_address'    => $ev['address'],
        'IS_BOOKMARK'      => $event->query("SELECT id FROM tbl_fav WHERE uid=$uid AND eid=$eid")->num_rows,
        'sponsore_list'    => getFirstSponsor($event, $eid),
        'member_list'      => getEventMembers($event, $eid),
        'total_member_list'=> (string)getTotalTickets($event, $eid)
    ];
}

//  Upcoming events
$eventlist = $event->query("SELECT * FROM tbl_event WHERE status=1 AND event_status='Pending' ORDER BY sdate");
while ($ev = $eventlist->fetch_assoc()) {
    $data = buildEventData($event, $ev, $uid, $timestamp, $chtime);
    if ($data) $v[] = $data;
}

//  Trending events (last 5)
$eventlistc = $eventlistc = $event->query("
    SELECT * 
    FROM tbl_event 
    WHERE status = 1 
      AND sdate = '$timestamp' 
    ORDER BY id DESC Limit 5
");
while ($evc = $eventlistc->fetch_assoc()) {
    $data = buildEventData($event, $evc, $uid, $timestamp, $chtime);
    if ($data) $sec[] = $data;
}

//  Nearby events
$eventlists = $event->query("
    SELECT (((ACOS(SIN(($lats*PI()/180)) * SIN((latitude*PI()/180))
    + COS(($lats*PI()/180)) * COS((latitude*PI()/180))
    * COS((($longs-longtitude)*PI()/180))))*180/PI())*60*1.1515*1.609344) AS distance,
    id,title,img,address,sdate,stime,is_booked,etime,cover_img
    FROM tbl_event
    WHERE status=1 ORDER BY distance
");
while ($evs = $eventlists->fetch_assoc()) {
    $data = buildEventData($event, $evs, $uid, $timestamp, $chtime);
    if ($data) {
        // Nearby events: include formatted date+time
        $date = date_create($evs['sdate']);
        $data['event_sdate'] = strtoupper(date_format($date, "dS M - D - ") . date("g:i A", strtotime($evs['stime'])));
        $data['event_img']   = $evs['img']; // nearby uses `img`
        $d[] = $data;
    }
}

//  This month events
$month = date("m");
$eve = $event->query("SELECT * FROM tbl_event WHERE status=1 AND MONTH(sdate) = $month");
while ($e = $eve->fetch_assoc()) {
    $data = buildEventData($event, $e, $uid, $timestamp, $chtime);
    if ($data) $pop[] = $data;
}

//  Wallet
$wallet = 0;
if ($uid > 0) {
    $tbwallet = $event->query("SELECT wallet FROM tbl_user WHERE id=$uid")->fetch_assoc();
    $wallet = $tbwallet['wallet'] ?? 0;
}

//  Settings
$main_data = $event->query("SELECT * FROM tbl_setting")->fetch_assoc();
$pols = [
    'id'       => $main_data['id'],
    'currency' => $main_data['currency'],
    'one_key'  => $main_data['one_key'],
    'one_hash' => $main_data['one_hash'],
    'scredit'  => $main_data['scredit'],
    'rcredit'  => $main_data['rcredit']
];

//  Final response
$kp = [
    'Catlist'        => $cp,
    'Main_Data'      => $pols,
    'trending_event' => $sec,
    'wallet'         => $wallet,
    'upcoming_event' => $v,
    'nearby_event'   => $d,
    'this_month_event'=> $pop
];

$returnArr = [
    "ResponseCode" => "200",
    "Result"       => "true",
    "ResponseMsg"  => "Home Data Get Successfully!",
    "HomeData"     => $kp
];

echo json_encode($returnArr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
