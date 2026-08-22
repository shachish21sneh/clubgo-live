<?php 
require dirname(dirname(__FILE__)) . '/include/eventconfig.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$loc_category_id = isset($data['loc_category_id']) ? intval($data['loc_category_id']) : null;
$uid = isset($data['uid']) ? $data['uid'] : '';
//$loc_category_id=2;
// $query = "SELECT v.*, 
//                  c.name as cityName, 
//                  GROUP_CONCAT(DISTINCT CONCAT(cu.id, ':', cu.name)) AS cuisines, 
//                  GROUP_CONCAT(DISTINCT CONCAT(f.id, ':', f.name)) AS facilities, 
//                  GROUP_CONCAT(DISTINCT CONCAT(k.id, ':', k.name)) AS known_for,
//                  GROUP_CONCAT(DISTINCT CONCAT(ve.loc_id, ':', v.loc_title, ':', v.loc_image)) AS similarvenue 
//           FROM tbl_veneu v
//           LEFT JOIN tbl_city c ON v.loc_city_id = c.id
//           LEFT JOIN tbl_cuisines cu ON FIND_IN_SET(cu.id, v.loc_cuisines_id)
//           LEFT JOIN tbl_facilities f ON FIND_IN_SET(f.id, v.loc_facilities_id)
//           LEFT JOIN tbl_known_for k ON FIND_IN_SET(k.id, v.loc_known_for)
//           LEFT JOIN tbl_veneu ve ON FIND_IN_SET(ve.loc_id, v.loc_similer_venue)
//           WHERE v.loc_status = 'A' AND FIND_IN_SET('".$loc_category_id."', v.loc_category_id)";

// $stmt = $event->prepare($query);

$query="SELECT 
v.*, 
c.name AS cityName, 
GROUP_CONCAT(DISTINCT CONCAT(cu.id, ':', cu.name)) AS cuisines,
GROUP_CONCAT(DISTINCT CONCAT(f.id, ':', f.name)) AS facilities,
GROUP_CONCAT(DISTINCT CONCAT(k.id, ':', k.name)) AS known_for,
(
  SELECT GROUP_CONCAT(DISTINCT CONCAT(ve.loc_id, ':', ve.loc_title, ':', ve.loc_image))
  FROM tbl_veneu ve
  WHERE FIND_IN_SET(ve.loc_id, v.loc_similer_venue)
) AS similarvenue
FROM tbl_veneu v
LEFT JOIN tbl_city c ON v.loc_city_id = c.id
LEFT JOIN tbl_cuisines cu ON FIND_IN_SET(cu.id, v.loc_cuisines_id)
LEFT JOIN tbl_facilities f ON FIND_IN_SET(f.id, v.loc_facilities_id)
LEFT JOIN tbl_known_for k ON FIND_IN_SET(k.id, v.loc_known_for)
WHERE v.loc_status = 'A'
AND FIND_IN_SET('".$loc_category_id."', REPLACE(v.loc_category_id, ' ', ''))
GROUP BY v.loc_id";
$stmt = $event->prepare($query);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $venues = [];

    while ($row = $result->fetch_assoc()) {
        $nav = [
            "venue_id" => $row['loc_id'],
            "venue_title" => $row['loc_title'],
            "venue_image" => $row['loc_image'],
            "venue_description" => $row['loc_description'],
            "venue_address" => $row['loc_customer_headlines'],
            "venue_category" => $row['loc_category_id'],
            "venue_days" => $row['loc_days'],
            "venue_start_time" => $row['loc_start_time'],
            "venue_end_time" => $row['loc_end_time'],
            "cityName" => $row['cityName'],
            "IS_BOOKMARK" => $event->query("SELECT * FROM tbl_fav_venue WHERE uid = $uid AND vid = " . $row['loc_id'])->num_rows
        ];

        // Convert 'id:name' strings into associative arrays
        $nav['cuisines'] = !empty($row['cuisines']) ? array_map(function ($item) {
            list($id, $name) = explode(':', $item);
            return ["id" => (int)$id, "name" => $name];
        }, explode(',', $row['cuisines'])) : [];

        $nav['facilities'] = !empty($row['facilities']) ? array_map(function ($item) {
            list($id, $name) = explode(':', $item);
            return ["id" => (int)$id, "name" => $name];
        }, explode(',', $row['facilities'])) : [];

        $nav['known_for'] = !empty($row['known_for']) ? array_map(function ($item) {
            list($id, $name) = explode(':', $item);
            return ["id" => (int)$id, "name" => $name];
        }, explode(',', $row['known_for'])) : [];

		$nav['similarvenue'] = !empty($row['similarvenue']) ? array_map(function ($item) {
            list($id, $name,$image) = explode(':', $item);
            return ["id" => (int)$id, "name" => $name, "loc_image" => $image,"eventcount"=>3,"rating"=>5.0];
        }, explode(',', $row['similarvenue'])) : [];

        $venues[] = $nav;
    }

    if (!empty($venues)) {
        echo json_encode([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Data fetched successfully",
            "SearchData" => $venues
        ]);
    } else {
        echo json_encode([
            "ResponseCode" => "404",
            "Result" => "false",
            "ResponseMsg" => "No data found"
        ]);
    }
}

?>
