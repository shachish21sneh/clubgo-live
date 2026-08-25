<?php 
error_reporting(E_ALL);
ini_set('display_errors', 0);
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
$uid = isset($data['uid']) ? (int)$data['uid'] : 0;
$eid = isset($data['eid']) ? (int)$data['eid'] : 0;

if ($eid <= 0) {
    echo json_encode(["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Event ID is required!"]);
    exit;
}

// 1. Fetch Event
$eventQuery = $event->query("SELECT * FROM tbl_event WHERE id=$eid AND status=1 LIMIT 1");
if (!$eventQuery || $eventQuery->num_rows === 0) {
    echo json_encode(["ResponseCode" => "404", "Result" => "false", "ResponseMsg" => "Event Not Found!"]);
    exit;
}

$ev = $eventQuery->fetch_assoc();

// 2. Pre-fetch category titles
$titles = [];
if (!empty($ev['cid'])) {
    $cidArray = array_map('intval', explode(',', $ev['cid']));
    $cidString = implode(',', array_filter($cidArray));
    if (!empty($cidString)) {
        $result1 = $event->query("SELECT title FROM tbl_cat WHERE id IN ($cidString)");
        if ($result1) while ($cat = $result1->fetch_assoc()) $titles[] = $cat['title'];
    }
}

// 3. Covers
$cover_img = [];
if (!empty($ev['cover_img'])) $cover_img[] = $ev['cover_img'];
$checkCovers = $event->query("SELECT img FROM tbl_cover WHERE eid=$eid AND status=1");
if ($checkCovers) while ($co = $checkCovers->fetch_assoc()) $cover_img[] = $co['img'];

// 4. Ticket price & bookings
$getprice = $event->query("SELECT price FROM tbl_type_price WHERE eid=$eid ORDER BY price ASC LIMIT 1")->fetch_assoc();
$ticketPrice = $getprice['price'] ?? 0;

$ticketSum = $event->query("SELECT SUM(ticket_book) AS books FROM tbl_type_price WHERE eid=$eid")->fetch_assoc();
$totalMembers = (string)($ticketSum['books'] ?? 0);

// 5. Bookmark
$isBookmark = 0;
if ($uid > 0) {
    $favCheck = $event->query("SELECT 1 FROM tbl_fav WHERE uid=$uid AND eid=$eid LIMIT 1");
    $isBookmark = ($favCheck && $favCheck->num_rows > 0) ? 1 : 0;
}

// 6. Member Avatars in 1 fast indexed JOIN
$members = [];
$mRes = $event->query("SELECT u.pro_pic FROM tbl_ticket t JOIN tbl_user u ON t.uid=u.id WHERE t.eid=$eid AND u.pro_pic IS NOT NULL AND u.pro_pic != '' GROUP BY u.id LIMIT 5");
if ($mRes) {
    while ($mr = $mRes->fetch_assoc()) $members[] = $mr['pro_pic'];
}

$date = date_create($ev['sdate']);
$v = [[
    'event_id'            => (string)$ev['id'],
    'event_title'         => $ev['title'],
    'event_img'           => $ev['img'],
    'event_cover_img'     => $cover_img,
    'event_sdate'         => $date ? date_format($date, "d F, Y") : '',
    'event_time_day'      => ($date ? date_format($date, "l") : '') . ', ' . date("g:i A", strtotime($ev['stime'])) . ' TO ' . date("g:i A", strtotime($ev['etime'])),
    'event_address_title' => $ev['place_name'] ?? '',
    'event_address'       => $ev['address'] ?? '',
    'event_latitude'      => $ev['latitude'] ?? '',
    'event_longtitude'    => $ev['longtitude'] ?? '',
    'event_about'         => $ev['description'] ?? '',
    'payment_type'        => $ev['payment_type'],
    'non_booking'         => $ev['non_booking'] ?? '',
    'user_number'         => $ev['user_number'] ?? '',
    'term_and_condition'  => $ev['term_and_condition'] ?? '',
    'disclaimer'          => $ev['disclaimer'] ?? '',
    'user_link'           => $ev['user_link'] ?? '',
    'dress_img'           => $ev['dress_img'] ?? '',
    'floor_img'           => $ev['floor_img'] ?? '',
    'menu_description'    => $ev['menu_description'] ?? '',
    'headline_json'       => $ev['headline_json'] ?? '',
    'ticket_price'        => $ticketPrice,
    'IS_BOOKMARK'         => $isBookmark,
    'category'            => $titles,
    'member_list'         => $members,
    'total_member_list'   => $totalMembers
]];

// 7. Gallery
$g = [];
$gal = $event->query("SELECT img FROM tbl_gallery WHERE eid=$eid AND status=1");
if ($gal) while ($row = $gal->fetch_assoc()) $g[] = $row['img'];

// 8. Sponsors
$s = [];
$spon = $event->query("SELECT id AS sponsore_id, img AS sponsore_img, title AS sponsore_title FROM tbl_sponsore WHERE eid=$eid AND status=1");
if ($spon) while ($row = $spon->fetch_assoc()) $s[] = $row;

// 9. Menu
$me = [];
$mrows = $event->query("SELECT vm.id, vm.img, mc.title AS menu_category_title 
                        FROM tbl_menu vm 
                        JOIN menu_category mc ON vm.menu_cat_id = mc.id 
                        WHERE vm.eid = $eid AND vm.status = 1");
if ($mrows) {
    while ($row2 = $mrows->fetch_assoc()) {
        $me[] = [
            'menu_id'    => $row2['id'],
            'menu_img'   => $row2['img'],
            'menu_title' => $row2['menu_category_title']
        ];
    }
}

// 10. Similar Events
$se = [];
if (!empty($ev['similer_event'])) {
    $simIds = array_map('intval', explode(',', $ev['similer_event']));
    $simString = implode(',', array_filter($simIds));
    if (!empty($simString)) {
        $simQuery = $event->query("SELECT id, title, img, sdate, stime, address, payment_type, cid FROM tbl_event WHERE status = 1 AND id IN ($simString) LIMIT 6");
        if ($simQuery) {
            while ($sim = $simQuery->fetch_assoc()) {
                $sDate = date_create($sim['sdate']);
                $sTime = date_create($sim['stime']);
                $se[] = [
                    'event_id'          => (string)$sim['id'],
                    'event_title'       => $sim['title'],
                    'event_img'         => $sim['img'],
                    'event_sdate'       => $sDate ? date_format($sDate, "d M") : '',
                    'event_address'     => $sim['address'] ?? '',
                    'payment_type'      => $sim['payment_type'],
                    'event_time'        => $sTime ? date_format($sTime, "h:i A") : '',
                    'category'          => [],
                    'IS_BOOKMARK'       => 0,
                    'member_list'       => [],
                    'total_member_list' => 0
                ];
            }
        }
    }
}

echo json_encode([
    "ResponseCode"   => "200",
    "Result"         => "true",
    "ResponseMsg"    => "Event Data Get Successfully!",
    "EventData"      => $v,
    "Event_gallery"  => $g,
    "Event_menu"     => $me,
    "Event_sponsore" => $s,
    "Event_similer"  => $se
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);