<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
$uid = $data['uid'];
$eid = $data['eid'];
//$uid = '35';
//$eid = '79799';
if($uid == '' or $eid == '')
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went wrong  try again !");
}
else 
{
	$v = array();
	$g = array();
	$s = array();
	$se = array();
	
	$eventlist = $event->query("select * from tbl_event where status=1 and id=".$eid."");
$nav = array();
while($ev = $eventlist->fetch_assoc())
{
	$cover_img = array();
	$nav['event_id'] = $ev['id'];
	$nav['event_title'] = $ev['title'];
	$nav['event_img'] = $ev['img'];
	$cover_img[] = $ev['cover_img'];
	$check = $event->query("select * from tbl_cover where eid=".$eid." and status=1");

	$cidList = $ev['cid']; // e.g., "1,2,3"
	$cidArray = explode(',', $cidList); // ['1', '2', '3']
	
	// Sanitize and prepare for SQL
	$cidArray = array_map('intval', $cidArray); // avoid SQL injection
	$cidString = implode(',', $cidArray); // "1,2,3"
	
	$result1 = $event->query("SELECT title FROM tbl_cat WHERE id IN ($cidString)");
	
	$titles = [];
	while ($cat = $result1->fetch_assoc()) {
		$titles[] = $cat['title'];
	}
	
	//$category = $event->query("SELECT title FROM `tbl_cat` WHERE id=".$ev['cid']."")->fetch_assoc();
	while($co = $check->fetch_assoc())
	{
		array_push($cover_img,$co['img']);
	}
	
	$nav['event_cover_img'] = $cover_img;
	$date=date_create($ev['sdate']);
	$nav['event_sdate'] = date_format($date,"d F, Y");
	$nav['event_time_day'] = date_format($date,"l").','.date("g:i A", strtotime($ev['stime'])).' TO '.date("g:i A", strtotime($ev['etime']));
	$nav['event_address_title'] = $ev['place_name'];
	$nav['event_address'] = $ev['address'];
	$nav['event_latitude'] = $ev['latitude'];
	$nav['event_longtitude'] = $ev['longtitude'];
	$nav['event_about'] = $ev['description'];
	$nav['payment_type']= $ev['payment_type'];
	$nav['non_booking']= $ev['non_booking'];
	$nav['user_number']= $ev['user_number'];
	$nav['term_and_condition']= $ev['term_and_condition'];
	$nav['disclaimer']= $ev['disclaimer'];
	$nav['user_link']=$ev['user_link'];
	$nav['dress_img']=$ev['dress_img'];
	$nav['floor_img']=$ev['floor_img'];
	$nav['menu_description']=$ev['menu_description'];
	$nav['headline_json']=$ev['headline_json'];

	$getprice = $event->query("select * from tbl_type_price where eid=".$eid." order by price limit 1")->fetch_assoc();
	$nav['ticket_price'] = $getprice['price'] ?? 0;
	$nav['IS_BOOKMARK'] = $event->query("select * from tbl_fav where uid=".$uid." and eid=".$ev['id']."")->num_rows;
	//$nav['category']=$category["title"];
	$nav['category']=$titles;
	$ulist = $event->query("SELECT uid,eid FROM `tbl_ticket` WHERE `eid` = ".$ev['id']." GROUP BY uid");
$member = array();
while($rp = $ulist->fetch_assoc())
{
	$getpic = $event->query("SELECT * FROM tbl_user WHERE id=".(int)$rp['uid'])->fetch_assoc();

	if ($getpic && !empty($getpic['pro_pic'])) {
	// user has profile picture
	$member[] = $getpic['pro_pic'];
	} else {
	// user not found OR pro_pic empty
	$member[] = "images/profile/pic1.jpg";
	}
}
$nav['member_list'] = $member;
$ticket = $event->query("SELECT sum(`ticket_book`) as books FROM `tbl_type_price` WHERE eid=".$ev['id']."")->fetch_assoc();
$nav['total_member_list'] = $ticket['books'] ?? "0";
	$v[] = $nav;
}

$gal = $event->query("select * from tbl_gallery where eid=".$eid." and status=1");
while($row = $gal->fetch_assoc())
{
	$g[] = $row['img'];
}

$spon = $event->query("select * from tbl_sponsore where eid=".$eid." and status=1");
$sponsore = array();
while($row = $spon->fetch_assoc())
{
	$sponsore['sponsore_id'] = $row['id'];
	$sponsore['sponsore_img'] = $row['img'];
	$sponsore['sponsore_title'] = $row['title'];
	$s[] = $sponsore;
}



$mrows = $event->query("SELECT 
vm.id,
vm.eid,
vm.menu_cat_id,
mc.title AS menu_category_title,
vm.img,
vm.status
FROM 
tbl_menu vm
JOIN 
menu_category mc ON vm.menu_cat_id = mc.id and vm.eid=" . $eid . " and vm.status=1");
        $menu = array();
        $me = [];
        while ($row2 = $mrows->fetch_assoc()) {
            $menu['menu_id'] = $row2['id'];
            $menu['menu_img'] = $row2['img'];
            $menu['menu_title'] = $row2['menu_category_title'];
            //$menu['sponsore_title'] = $row['title'];
            $me[] = $menu;
        }

$eventQuery = $event->query("SELECT similer_event FROM tbl_event WHERE id = $eid and status=1");
$eventData = $eventQuery->fetch_assoc();
$navn = array();
if ($eventData && $eventData['similer_event']) {
    $similier_ids = explode(',', $eventData['similer_event']); // Convert CSV to array

    // Build SQL query dynamically
    $conditions = [];
    foreach ($similier_ids as $id) {
        $conditions[] = "FIND_IN_SET('$id', id)";
    }
	$sql = "SELECT * FROM tbl_event WHERE status = 1 AND (" . implode(" OR ", $conditions) . ")";
    $result = $event->query($sql);
    while ($row = $result->fetch_assoc()) {

		$cidList = $row['cid']; // e.g., "1,2,3"
		$cidArray = explode(',', $cidList); // ['1', '2', '3']
		
		// Sanitize and prepare for SQL
		$cidArray = array_map('intval', $cidArray); // avoid SQL injection
		$cidString = implode(',', $cidArray); // "1,2,3"
		
		$result1 = $event->query("SELECT title FROM tbl_cat WHERE id IN ($cidString)");
		
		$titles = [];
		while ($cat = $result1->fetch_assoc()) {
			$titles[] = $cat['title'];
		}

		$navn['event_id'] = $row['id'];
		$navn['event_title'] = $row['title'];
		$navn['event_img'] = $row['img'];
		$date=date_create($row['sdate']);
		$navn['event_sdate'] = date_format($date,"d M");
		$time = date_create($row['stime']);
		$navn['event_address'] = $row['address'];
		$navn['payment_type'] = $row['payment_type'];
		$navn['event_time'] = date_format($time, "h:i A");
		//$navn['category'] = $event->query("select title from tbl_cat where id=".$row['cid']."")->fetch_assoc();
		$navn['category'] =$titles;
		$navn['IS_BOOKMARK'] = $event->query("select * from tbl_fav where uid=".$uid." and eid=".$row['id']."")->num_rows;
		$sponn = $event->query("select * from tbl_sponsore where eid=".$row['id']." and status=1");
	
	$ulistn = $event->query("SELECT uid,eid FROM `tbl_ticket` WHERE `eid` = ".$row['id']." GROUP BY uid");
	$member = array();
	while($rpn = $ulistn->fetch_assoc())
	{
	$getpic = $event->query("SELECT * FROM tbl_user WHERE id=".(int)$rpn['uid'])->fetch_assoc();

	if ($getpic && !empty($getpic['pro_pic'])) {
	// user has profile picture
	$member[] = $getpic['pro_pic'];
	} else {
	// user not found OR pro_pic empty
	$member[] = "images/profile/pic1.jpg";
	}
	}
	$navn['member_list'] = $member;
	$ticket = $event->query("SELECT sum(`ticket_book`) as books FROM `tbl_type_price` WHERE eid=".$row['id']."")->fetch_assoc();
	$navn['total_member_list'] = $ticket['books'];
	$se[] = $navn;
    }
}

$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Event Data Get Successfully!","EventData"=>$v,"Event_gallery"=>$g,"Event_menu"=>$me,"Event_sponsore"=>$s,"Event_similer"=>$se);
}
echo json_encode($returnArr);