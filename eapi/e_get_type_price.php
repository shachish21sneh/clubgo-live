<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$uid = $data['uid'];
$eid = $data['eid'];
if($uid == '' or $eid == '')
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went wrong  try again !");
}
else 
{
	$v = array();
	$eventlist = $event->query("select * from tbl_event where status=1 and id=".$eid."");
    $nav = array();
while($ev = $eventlist->fetch_assoc())
{


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

	$cover_img = array();
	$nav['event_id'] = $ev['id'];
	$nav['event_title'] = $ev['title'];
	$nav['event_img'] = $ev['img'];
	$nav['event_sdate'] = $ev['sdate'];
	$nav['event_edate'] = $ev['edate'];
	$nav['event_stime'] = $ev['stime'];
	$nav['event_address'] = $ev['address'];
	//$category = $event->query("SELECT title FROM `tbl_cat` WHERE id=".$ev['cid']."")->fetch_assoc();
	//$nav['category']=$category["title"];
	$nav['category']=$titles;
	$tick = array();
	$ticket = array();
	$getprice = $event->query("select * from tbl_type_price where eid=".$eid."");
	while($row = $getprice->fetch_assoc())
	{
		$tick['typeid'] = $row['id'];
		$tick['ticket_type'] = $row['type'];
		$tick['ticket_name'] = $row['name'];
		$tick['ticket_details'] = $row['details'];
		$tick['ticket_price'] = $row['price'];
		$tick['ticket_stime'] = $row['start_time'];
		$tick['ticket_limit'] = $row['tlimit'] - $row['ticket_book'];
		$tick['couple_ratio'] = $row['couple_ratio'];
		$tick['entry_type'] = $row['entry_type'];
		$tick['ticket_singlename'] = $row['single_name'];
		
		$tick['ticket_cprice']     = !empty($row['couple_price']) ? $row['couple_price'] : 0;
		$tick['ticket_cdiscount']  = !empty($row['discount_couple_price']) ? $row['discount_couple_price'] : 0;
		$tick['ticket_cdesc']      = $row['description_couple'];

		$tick['ticket_fprice']     = !empty($row['female_price']) ? $row['female_price'] : 0;
		$tick['ticket_fdesc']      = $row['description_female'];
		$tick['ticket_fdiscount']  = !empty($row['discount_female_price']) ? $row['discount_female_price'] : 0;

		$tick['ticket_mprice']     = !empty($row['male_price']) ? $row['male_price'] : 0;
		$tick['ticket_mdiscount']  = !empty($row['discount_male_price']) ? $row['discount_male_price'] : 0;
		$tick['ticket_mdesc'] = $row['description_male'];

		$ticket[] = $tick;
	}
	$nav['ticketlist'] = $ticket;
	$nav['event_disclaimer'] = $ev['disclaimer'];
	$nav['event_tax'] = $set['tax'];
	$v[] = $nav;
}

$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Event Data Get Successfully!","EventData"=>$v);
}
echo json_encode($returnArr);