<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);

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

$v = [];

$query = "
    SELECT v.*, c.name AS cityName, 
           GROUP_CONCAT(DISTINCT CONCAT(cu.id, ':', cu.name)) AS cuisines, 
           GROUP_CONCAT(DISTINCT CONCAT(f.id, ':', f.name)) AS facilities, 
           GROUP_CONCAT(DISTINCT CONCAT(k.id, ':', k.name)) AS known_for
    FROM tbl_veneu v
    LEFT JOIN tbl_city c ON v.loc_city_id = c.id
    LEFT JOIN tbl_cuisines cu ON FIND_IN_SET(cu.id, v.loc_cuisines_id)
    LEFT JOIN tbl_facilities f ON FIND_IN_SET(f.id, v.loc_facilities_id)
    LEFT JOIN tbl_known_for k ON FIND_IN_SET(k.id, v.loc_known_for)
    WHERE v.loc_status = 'A'
    GROUP BY v.loc_id
    ORDER BY v.loc_id DESC
";

$venuelist = $event->query($query);

if (!$venuelist) {
    echo json_encode([
        "ResponseCode" => "500",
        "Result" => "false",
        "ResponseMsg" => "Query Failed: " . $event->error
    ]);
    exit;
}

while ($venue = $venuelist->fetch_assoc()) {
    // Bookmark check
    $bookmarkRes = $event->query("SELECT id FROM tbl_fav_venue WHERE uid = {$uid} AND vid = " . (int)$venue['loc_id']);
    $isBookmark = $bookmarkRes ? $bookmarkRes->num_rows : 0;

    $nav = [
        "venue_id" => $venue['loc_id'],
        "venue_title" => $venue['loc_title'],
        "venue_image" => $venue['loc_image'],
        "venue_description" => $venue['loc_description'],
        "venue_address" => $venue['loc_customer_headlines'],
        "venue_category" => $venue['loc_category_id'],
        "venue_days" => $venue['loc_days'],
        "venue_start_time" => $venue['loc_start_time'],
        "venue_end_time" => $venue['loc_end_time'],
        "cityName" => $venue['cityName'],
        "IS_BOOKMARK" => $isBookmark
    ];

    // Safely split cuisines
    $nav['cuisines'] = [];
    if (!empty($venue['cuisines'])) {
        foreach (explode(',', $venue['cuisines']) as $item) {
            if (strpos($item, ':') !== false) {
                list($id, $name) = explode(':', $item, 2);
                $nav['cuisines'][] = ["id" => (int)$id, "name" => $name];
            }
        }
    }

    // Safely split facilities
    $nav['facilities'] = [];
    if (!empty($venue['facilities'])) {
        foreach (explode(',', $venue['facilities']) as $item) {
            if (strpos($item, ':') !== false) {
                list($id, $name) = explode(':', $item, 2);
                $nav['facilities'][] = ["id" => (int)$id, "name" => $name];
            }
        }
    }

    // Safely split known_for
    $nav['known_for'] = [];
    if (!empty($venue['known_for'])) {
        foreach (explode(',', $venue['known_for']) as $item) {
            if (strpos($item, ':') !== false) {
                list($id, $name) = explode(':', $item, 2);
                $nav['known_for'][] = ["id" => (int)$id, "name" => $name];
            }
        }
    }

    $v[] = $nav;
	//print_r($v);
}

if (empty($v)) {
    // Ensure UTF-8 safe
array_walk_recursive($v, function (&$item) {
    if (is_string($item)) {
        $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
    }
});

$json = json_encode([
    "ResponseCode" => "400",
    "Result" => "true",
    "ResponseMsg" => "Venue List not found!",
    "SearchData" => $v
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($json === false) {
    echo "JSON Encode Error: " . json_last_error_msg();
} else {
    header('Content-Type: application/json; charset=utf-8');
    echo $json;
}

} else {
	// Ensure UTF-8 safe
array_walk_recursive($v, function (&$item) {
    if (is_string($item)) {
        $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
    }
});

$json = json_encode([
    "ResponseCode" => "200",
    "Result" => "true",
    "ResponseMsg" => "Venue List Retrieved Successfully!",
    "SearchData" => $v
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($json === false) {
    echo "JSON Encode Error: " . json_last_error_msg();
} else {
    header('Content-Type: application/json; charset=utf-8');
    echo $json;
}

}