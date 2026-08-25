<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
require dirname(dirname(__FILE__)) . '/include/eventconfig.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

$loc_id = isset($data['loc_id']) ? intval($data['loc_id']) : 0;
$uid = isset($data['uid']) ? intval($data['uid']) : 0;

if ($loc_id <= 0) {
    echo json_encode(["ResponseCode" => "400", "Result" => "false", "ResponseMsg" => "Venue ID is required"]);
    exit;
}

// 1. Fetch Venue Record
$stmt = $event->prepare("SELECT v.*, c.name AS cityName FROM tbl_veneu v LEFT JOIN tbl_city c ON v.loc_city_id = c.id WHERE v.loc_id = ? AND v.loc_status = 'A' LIMIT 1");
$stmt->bind_param("i", $loc_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    echo json_encode(["ResponseCode" => "404", "Result" => "false", "ResponseMsg" => "No data found"]);
    exit;
}

$row = $result->fetch_assoc();

// 2. Pre-load taxonomy maps
$cuisinesMap = [];
$cuRes = $event->query("SELECT id, name FROM tbl_cuisines");
if ($cuRes) while ($r = $cuRes->fetch_assoc()) $cuisinesMap[(int)$r['id']] = $r['name'];

$facilitiesMap = [];
$faRes = $event->query("SELECT id, name FROM tbl_facilities");
if ($faRes) while ($r = $faRes->fetch_assoc()) $facilitiesMap[(int)$r['id']] = $r['name'];

$knownForMap = [];
$knRes = $event->query("SELECT id, name FROM tbl_known_for");
if ($knRes) while ($r = $knRes->fetch_assoc()) $knownForMap[(int)$r['id']] = $r['name'];

$packagesMap = [];
$pkRes = $event->query("SELECT id, name FROM tbl_package_items");
if ($pkRes) while ($r = $pkRes->fetch_assoc()) $packagesMap[(int)$r['id']] = $r['name'];

$catMap = [];
$catRes = $event->query("SELECT id, title FROM tbl_cat");
if ($catRes) while ($r = $catRes->fetch_assoc()) $catMap[(int)$r['id']] = $r['title'];

// Map taxonomy
$cList = [];
if (!empty($row['loc_cuisines_id'])) {
    foreach (explode(',', $row['loc_cuisines_id']) as $cId) {
        $cId = (int)trim($cId);
        if (isset($cuisinesMap[$cId])) $cList[] = ["id" => $cId, "name" => $cuisinesMap[$cId]];
    }
}
$row['cuisines'] = $cList;

$fList = [];
if (!empty($row['loc_facilities_id'])) {
    foreach (explode(',', $row['loc_facilities_id']) as $fId) {
        $fId = (int)trim($fId);
        if (isset($facilitiesMap[$fId])) $fList[] = ["id" => $fId, "name" => $facilitiesMap[$fId]];
    }
}
$row['facilities'] = $fList;

$kList = [];
if (!empty($row['loc_known_for'])) {
    foreach (explode(',', $row['loc_known_for']) as $kId) {
        $kId = (int)trim($kId);
        if (isset($knownForMap[$kId])) $kList[] = ["id" => $kId, "name" => $knownForMap[$kId]];
    }
}
$row['known_for'] = $kList;

$pList = [];
if (!empty($row['loc_package_id'])) {
    foreach (explode(',', $row['loc_package_id']) as $pId) {
        $pId = (int)trim($pId);
        if (isset($packagesMap[$pId])) $pList[] = ["id" => $pId, "name" => $packagesMap[$pId]];
    }
}
$row['pakages'] = $pList;

// Format Dates
$date = date_create($row['loc_from_date']);
$dateend = date_create($row['loc_to_date']);
$row['loc_days_to'] = ($date && $dateend) ? (date_format($date, "l") . ' TO ' . date_format($dateend, "l")) : '';
$row['loc_time_day'] = ($date && $dateend) ? (date_format($date, "l") . ',' . date("g:i A", strtotime($row['loc_start_time'])) . ' TO ' . date_format($dateend, "l") . ',' . date("g:i A", strtotime($row['loc_end_time']))) : '';

// 3. Similar Venues
$similarVenues = [];
if (!empty($row['loc_similer_venue'])) {
    $sIds = array_map('intval', explode(',', $row['loc_similer_venue']));
    $sIdString = implode(',', array_filter($sIds));
    if (!empty($sIdString)) {
        $svRes = $event->query("SELECT v.loc_id, v.loc_title, v.loc_image, c.name AS city 
                                FROM tbl_veneu v 
                                LEFT JOIN tbl_city c ON v.loc_city_id = c.id 
                                WHERE v.loc_id IN ($sIdString) AND v.loc_status = 'A'");
        if ($svRes) {
            while ($sv = $svRes->fetch_assoc()) {
                $similarVenues[] = [
                    "id"         => (int)$sv['loc_id'],
                    "name"       => $sv['loc_title'],
                    "loc_image"  => $sv['loc_image'],
                    "eventcount" => 0,
                    "city"       => $sv['city'] ?? '',
                    "rating"     => 5.0
                ];
            }
        }
    }
}
$row['similarvenue'] = $similarVenues;

// 4. Hosted Events at Venue
$se = [];
$evResult = $event->query("SELECT id, title, img, sdate, stime, address, payment_type, cid FROM tbl_event WHERE loc_id = $loc_id AND status = 1 ORDER BY sdate ASC LIMIT 10");
if ($evResult && $evResult->num_rows > 0) {
    // User bookmarks
    $userBookmarks = [];
    if ($uid > 0) {
        $favRes = $event->query("SELECT eid FROM tbl_fav WHERE uid=$uid");
        if ($favRes) while ($fr = $favRes->fetch_assoc()) $userBookmarks[(int)$fr['eid']] = 1;
    }

    while ($rownew = $evResult->fetch_assoc()) {
        $eid = (int)$rownew['id'];
        $cTitles = [];
        if (!empty($rownew['cid'])) {
            foreach (explode(',', $rownew['cid']) as $catId) {
                $catId = (int)trim($catId);
                if (isset($catMap[$catId])) $cTitles[] = $catMap[$catId];
            }
        }

        // Tickets & Avatars
        $mRes = $event->query("SELECT u.pro_pic FROM tbl_ticket t JOIN tbl_user u ON t.uid=u.id WHERE t.eid=$eid AND u.pro_pic IS NOT NULL AND u.pro_pic != '' GROUP BY t.uid LIMIT 3");
        $members = [];
        if ($mRes) while ($mr = $mRes->fetch_assoc()) $members[] = $mr['pro_pic'];

        $ticket = $event->query("SELECT SUM(ticket_book) as books FROM tbl_type_price WHERE eid = $eid")->fetch_assoc();

        $se[] = [
            'event_id'          => (string)$eid,
            'event_title'       => $rownew['title'],
            'event_img'         => $rownew['img'],
            'event_sdate'       => date("d M", strtotime($rownew['sdate'])),
            'event_time'        => date("h:i A", strtotime($rownew['stime'])),
            'event_address'     => $rownew['address'],
            'payment_type'      => $rownew['payment_type'],
            'category'          => $cTitles,
            'IS_BOOKMARK'       => isset($userBookmarks[$eid]) ? 1 : 0,
            'member_list'       => $members,
            'total_member_list' => (int)($ticket['books'] ?? 0)
        ];
    }
}
$row['similar_events'] = $se;

// 5. Venue Gallery
$g = [];
$gal = $event->query("SELECT img FROM tbl_venue_gallery WHERE vid = $loc_id AND status = 1");
if ($gal) while ($row1 = $gal->fetch_assoc()) $g[] = $row1['img'];
$row['gallery'] = $g;

// 6. Venue Menu
$me = [];
$mrows = $event->query("SELECT vm.id, vm.img, mc.title AS menu_category_title 
                        FROM tbl_venue_menu vm 
                        JOIN menu_category mc ON vm.menu_cat_id = mc.id 
                        WHERE vm.vid = $loc_id AND vm.status = 1");
if ($mrows) {
    while ($row2 = $mrows->fetch_assoc()) {
        $me[] = [
            'menu_id'    => $row2['id'],
            'menu_img'   => $row2['img'],
            'menu_title' => $row2['menu_category_title']
        ];
    }
}
$row['menu'] = $me;

echo json_encode([
    "ResponseCode" => "200",
    "Result"       => "true",
    "ResponseMsg"  => "Data fetched successfully",
    "venues"       => [$row]
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);