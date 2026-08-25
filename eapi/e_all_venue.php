<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require dirname(dirname(__FILE__)) . '/include/eventconfig.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
$uid = isset($data['uid']) ? intval($data['uid']) : 0;

if ($uid <= 0) {
    echo json_encode([
        "ResponseCode" => "401",
        "Result" => "false",
        "ResponseMsg" => "Something went wrong, try again!"
    ]);
    exit;
}

// 1. Pre-load taxonomy dictionaries
$cuisinesMap = [];
$cuRes = $event->query("SELECT id, name FROM tbl_cuisines");
if ($cuRes) {
    while ($r = $cuRes->fetch_assoc()) $cuisinesMap[(int)$r['id']] = $r['name'];
}

$facilitiesMap = [];
$faRes = $event->query("SELECT id, name FROM tbl_facilities");
if ($faRes) {
    while ($r = $faRes->fetch_assoc()) $facilitiesMap[(int)$r['id']] = $r['name'];
}

$knownForMap = [];
$knRes = $event->query("SELECT id, name FROM tbl_known_for");
if ($knRes) {
    while ($r = $knRes->fetch_assoc()) $knownForMap[(int)$r['id']] = $r['name'];
}

// 2. Pre-load user bookmarks
$favVenueMap = [];
if ($uid > 0) {
    $fvRes = $event->query("SELECT vid FROM tbl_fav_venue WHERE uid=$uid");
    if ($fvRes) {
        while ($r = $fvRes->fetch_assoc()) $favVenueMap[(int)$r['vid']] = 1;
    }
}

// 3. Fast indexed query
$query = "SELECT v.*, c.name AS cityName 
          FROM tbl_veneu v 
          LEFT JOIN tbl_city c ON v.loc_city_id = c.id 
          WHERE v.loc_status = 'A' 
          ORDER BY v.loc_id DESC";

$venuelist = $event->query($query);
$v = [];

if ($venuelist) {
    while ($venue = $venuelist->fetch_assoc()) {
        $vid = (int)$venue['loc_id'];
        
        // Split cuisines
        $cList = [];
        if (!empty($venue['loc_cuisines_id'])) {
            foreach (explode(',', $venue['loc_cuisines_id']) as $cId) {
                $cId = (int)trim($cId);
                if (isset($cuisinesMap[$cId])) {
                    $cList[] = ["id" => $cId, "name" => $cuisinesMap[$cId]];
                }
            }
        }

        // Split facilities
        $fList = [];
        if (!empty($venue['loc_facilities_id'])) {
            foreach (explode(',', $venue['loc_facilities_id']) as $fId) {
                $fId = (int)trim($fId);
                if (isset($facilitiesMap[$fId])) {
                    $fList[] = ["id" => $fId, "name" => $facilitiesMap[$fId]];
                }
            }
        }

        // Split known_for
        $kList = [];
        if (!empty($venue['loc_known_for'])) {
            foreach (explode(',', $venue['loc_known_for']) as $kId) {
                $kId = (int)trim($kId);
                if (isset($knownForMap[$kId])) {
                    $kList[] = ["id" => $kId, "name" => $knownForMap[$kId]];
                }
            }
        }

        $v[] = [
            "venue_id"          => $venue['loc_id'],
            "venue_title"       => $venue['loc_title'],
            "venue_image"       => $venue['loc_image'],
            "venue_description" => $venue['loc_description'],
            "venue_address"     => $venue['loc_customer_headlines'],
            "venue_category"    => $venue['loc_category_id'],
            "venue_days"        => $venue['loc_days'],
            "venue_start_time"  => $venue['loc_start_time'],
            "venue_end_time"    => $venue['loc_end_time'],
            "cityName"          => $venue['cityName'] ?? '',
            "IS_BOOKMARK"       => isset($favVenueMap[$vid]) ? 1 : 0,
            "cuisines"          => $cList,
            "facilities"        => $fList,
            "known_for"         => $kList
        ];
    }
}

if (empty($v)) {
    echo json_encode([
        "ResponseCode" => "400",
        "Result"       => "true",
        "ResponseMsg"  => "Venue List not found!",
        "SearchData"   => []
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} else {
    echo json_encode([
        "ResponseCode" => "200",
        "Result"       => "true",
        "ResponseMsg"  => "Venue List Retrieved Successfully!",
        "SearchData"   => $v
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}