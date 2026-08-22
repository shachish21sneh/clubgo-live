<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require dirname(dirname(__FILE__)) . '/include/eventconfig.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$loc_id = isset($data['loc_id']) ? intval($data['loc_id']) : null;
$uid = $data['uid'];
$query = "SELECT v.*, 
                 c.name as cityName, 
                 GROUP_CONCAT(DISTINCT CONCAT(cu.id, ':', cu.name)) AS cuisines, 
                 GROUP_CONCAT(DISTINCT CONCAT(f.id, ':', f.name)) AS facilities, 
                 GROUP_CONCAT(DISTINCT CONCAT(k.id, ':', k.name)) AS known_for,
                 GROUP_CONCAT(DISTINCT CONCAT(p.id, ':', p.name)) AS pakages,
                 GROUP_CONCAT(DISTINCT CONCAT(ve.loc_id, '|', ve.loc_title, '|', ve.loc_image, '|', ve.loc_city_id) SEPARATOR '||') AS similarvenue 
          FROM tbl_veneu v
          LEFT JOIN tbl_city c ON v.loc_city_id = c.id
          LEFT JOIN tbl_cuisines cu ON FIND_IN_SET(cu.id, v.loc_cuisines_id)
          LEFT JOIN tbl_facilities f ON FIND_IN_SET(f.id, v.loc_facilities_id)
          LEFT JOIN tbl_known_for k ON FIND_IN_SET(k.id, v.loc_known_for)
          LEFT JOIN tbl_package_items p ON FIND_IN_SET(p.id, v.loc_package_id)
          LEFT JOIN tbl_veneu ve ON FIND_IN_SET(ve.loc_id, COALESCE(NULLIF(v.loc_similer_venue, ''), '0'))  
                                 AND ve.loc_status = 'A'
          WHERE v.loc_status = 'A'";


if ($loc_id !== null) {
    $query .= " AND v.loc_id = ?";
}

$query .= " GROUP BY v.loc_id";

$stmt = $event->prepare($query);

if ($loc_id !== null) {
    $stmt->bind_param("i", $loc_id);
}
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $venues = [];

    while ($row = $result->fetch_assoc()) {
        $date = date_create($row['loc_from_date']);
        $dateend = date_create($row['loc_to_date']);
        $row['loc_days_to'] = date_format($date, "l") . ' TO ' . date_format($dateend, "l");
        $row['loc_time_day'] = date_format($date, "l") . ',' . date("g:i A", strtotime($row['loc_start_time'])) . ' TO ' . date_format($dateend, "l") . ',' . date("g:i A", strtotime($row['loc_end_time']));

        // Convert 'id:name' strings into associative arrays
        $row['cuisines'] = !empty($row['cuisines']) ? array_map(function ($item) {
            list($id, $name) = explode(':', $item);
            return ["id" => (int) $id, "name" => $name];
        }, explode(',', $row['cuisines'])) : [];

        $row['facilities'] = !empty($row['facilities']) ? array_map(function ($item) {
            list($id, $name) = explode(':', $item);
            return ["id" => (int) $id, "name" => $name];
        }, explode(',', $row['facilities'])) : [];

        $row['known_for'] = !empty($row['known_for']) ? array_map(function ($item) {
            list($id, $name) = explode(':', $item);
            return ["id" => (int) $id, "name" => $name];
        }, explode(',', $row['known_for'])) : [];

		$row['pakages'] = !empty($row['pakages'])
		? array_values(array_filter(array_map(function ($item) {
		$parts = explode(':', $item, 2);
		// if not valid "id:name" format, skip it
		if (count($parts) < 2) {
			return null;
		}
		return [
			"id"   => (int) $parts[0],
			"name" => $parts[1]
		];
		}, explode(',', $row['pakages']))))
		: [];

        $row['similarvenue'] = !empty($row['similarvenue']) ? array_map(function ($item) use ($event) {
            $parts = explode('|', $item, 4); // Limit to 3 parts

            $cityname = $event->query("SELECT `name` FROM `tbl_city` WHERE id=" . $parts[3] . "")->fetch_assoc();
            $events_count = $event->query("SELECT count(*) as eventscount FROM `tbl_event` WHERE loc_id=" . $parts[0] . "")->fetch_assoc();

            return [
                "id" => (int) $parts[0],
                "name" => $parts[1],
                "loc_image" => $parts[2],
                "eventcount" => (int) $events_count["eventscount"],
                "city" => $cityname["name"],
                "rating" => 5.0
            ];
        }, explode('||', $row['similarvenue'])) : [];



        $sql = "SELECT * FROM tbl_event WHERE status = 1 AND loc_id=" . $row['loc_id'] . "";
        $result = $event->query($sql);
        $se = []; // initialize array

        while ($rownew = $result->fetch_assoc()) {
            $navn = []; // reset for each event

            $navn['event_id'] = $rownew['id'];
            $navn['event_title'] = $rownew['title'];
            $navn['event_img'] = $rownew['img'];

            $navn['event_sdate'] = date("d M", strtotime($rownew['sdate']));
            $navn['event_time'] = date("h:i A", strtotime($rownew['stime']));

            $navn['event_address'] = $rownew['address'];
            $navn['payment_type'] = $rownew['payment_type'];



            $cidList = $rownew['cid']; // e.g., "1,2,3"
            $cidArray = explode(',', $cidList); // ['1', '2', '3']

            // Sanitize and prepare for SQL
            $cidArray = array_map('intval', $cidArray); // avoid SQL injection
            $cidString = implode(',', $cidArray); // "1,2,3"

            $result1 = $event->query("SELECT title FROM tbl_cat WHERE id IN ($cidString)");

            $titles = [];
            while ($cat = $result1->fetch_assoc()) {
                $titles[] = $cat['title'];
            }

            //  $cat = $event->query("SELECT title FROM tbl_cat WHERE id = ".$rownew['cid'])->fetch_assoc();
            //$navn['category'] = $cat['title'] ?? '';
            $navn['category'] = $titles;




            $is_bookmark = $event->query("SELECT id FROM tbl_fav WHERE uid = $uid AND eid = " . $rownew['id'])->num_rows;
            $navn['IS_BOOKMARK'] = $is_bookmark;

            // Get member profile pictures
            $ulistn = $event->query("SELECT uid FROM tbl_ticket WHERE eid = " . $rownew['id'] . " GROUP BY uid");
            $member = [];
            while ($rpn = $ulistn->fetch_assoc()) {
                $getpic = $event->query("SELECT pro_pic FROM tbl_user WHERE id = " . $rpn['uid'])->fetch_assoc();
                if (!empty($getpic['pro_pic'])) {
                    $member[] = $getpic['pro_pic'];
                }
            }
            $navn['member_list'] = $member;

            $ticket = $event->query("SELECT SUM(ticket_book) as books FROM tbl_type_price WHERE eid = " . $rownew['id'])->fetch_assoc();
            $navn['total_member_list'] = $ticket['books'] ?? 0;

            $se[] = $navn;
        }

        $g = [];
        $gal = $event->query("select * from tbl_venue_gallery where vid=" . $row['loc_id'] . " and status=1");
        while ($row1 = $gal->fetch_assoc()) {
            $g[] = $row1['img'];
        }



        $mrows = $event->query("SELECT 
vm.id,
vm.vid,
vm.menu_cat_id,
mc.title AS menu_category_title,
vm.img,
vm.status
FROM 
tbl_venue_menu vm
JOIN 
menu_category mc ON vm.menu_cat_id = mc.id and vm.vid=" . $row['loc_id'] . " and vm.status=1");
        $menu = array();
        $me = [];
        while ($row2 = $mrows->fetch_assoc()) {
            $menu['menu_id'] = $row2['id'];
            $menu['menu_img'] = $row2['img'];
            $menu['menu_title'] = $row2['menu_category_title'];
            //$menu['sponsore_title'] = $row['title'];
            $me[] = $menu;
        }



        $row['similar_events'] = $se;
        $row['gallery'] = $g;
        $row['menu'] = $me;
        $venues[] = $row;
    }

    if (!empty($venues)) {
        echo json_encode([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Data fetched successfully",
            "venues" => $venues
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