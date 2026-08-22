<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$uid = $data['uid'];
$tid = $data['tid'];
if($uid == '' or $tid == '')
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went wrong  try again !");
}
else 
{
	$nav = array();
	$g = array();
	$s = array();
	
	$tdata = $event->query("select * from tbl_ticket where  id=".$tid."")->fetch_assoc();
	$eventlist = $event->query("select * from tbl_event where status=1 and id=".$tdata['eid']."")->fetch_assoc();

	$cover_img = array();
	$nav['event_id'] = $eventlist['id'];
	$nav['event_title'] = $eventlist['title'];
	$nav['event_img'] = $eventlist['img'];
	$nav['ticket_type'] = $tdata['type'];
	$cover_img[] = $eventlist['cover_img'];
	$check = $event->query("select * from tbl_cover where eid=".$tdata['eid']." and status=1");
	while($co = $check->fetch_assoc())
	{
		array_push($cover_img,$co['img']);
	}
	
	$nav['event_cover_img'] = $cover_img;
	$date=date_create($eventlist['sdate']);
	$nav['event_sdate'] = date_format($date,"d F, Y");
	$nav['event_time_day'] = date_format($date,"l").','.date("g:i A", strtotime($eventlist['stime'])).' TO '.date("g:i A", strtotime($eventlist['etime']));
	$nav['event_address_title'] = $eventlist['place_name'];
	$nav['event_address'] = $eventlist['address'];
	$nav['event_latitude'] = $eventlist['latitude'];
	$nav['event_longtitude'] = $eventlist['longtitude'];
	$nav['event_about'] = $eventlist['description'];
	$getprice = $event->query("select * from tbl_type_price where eid=".$tdata['eid']." order by price limit 1")->fetch_assoc();
	$nav['ticket_price'] = $getprice['price'];
	$nav['IS_BOOKMARK'] = $event->query("select * from tbl_fav where uid=".$uid." and eid=".$eventlist['id']."")->num_rows;
	


$gal = $event->query("select * from tbl_gallery where eid=".$tdata['eid']." and status=1");
while($row = $gal->fetch_assoc())
{
	$g[] = $row['img'];
}

$ulist = $event->query("SELECT uid,eid FROM `tbl_ticket` WHERE `eid` = ".$tdata['eid']." GROUP BY uid");
$member = array();
while($rp = $ulist->fetch_assoc())
{
	$getpic = $event->query("select * from tbl_user where id=".$rp['uid']."")->fetch_assoc();
	if($getpic['pro_pic'] == '')
	{
	}
	else 
	{
	$member[] = $getpic['pro_pic'];
	}
}
$spon = $event->query("select * from tbl_sponsore where eid=".$tdata['eid']." and status=1");
$sponsore = array();
while($row = $spon->fetch_assoc())
{
	$sponsore['sponsore_id'] = $row['id'];
	$sponsore['sponsore_img'] = $row['img'];
	$sponsore['sponsore_title'] = $row['title'];
	$s[] = $sponsore;
}



$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Event Data Get Successfully!","EventData"=>$nav,"Event_gallery"=>$g,"Event_sponsore"=>$s,"Member"=>$member);
}
echo json_encode($returnArr);