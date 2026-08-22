<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$uid = $data['uid'];
$title = $data['title'];
if($uid == '' or $title == '')
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went wrong  try again !");
}
else 
{
	$v = array();
	


$eventlist = $event->query("select * from tbl_event where status=1 and title COLLATE utf8_general_ci like '%".$title."%' order by id desc");
$nav = array();
while($ev = $eventlist->fetch_assoc())
{
	$nav['event_id'] = $ev['id'];
	$nav['event_title'] = $ev['title'];
	$nav['event_img'] = $ev['img'];
	$date=date_create($ev['sdate']);
	$nav['event_sdate'] = date_format($date,"d F");
	$nav['event_address'] = $ev['address'];
	$nav['IS_BOOKMARK'] = $event->query("select * from tbl_fav where uid=".$uid." and eid=".$ev['id']."")->num_rows;
	$spon = $event->query("select * from tbl_sponsore where eid=".$ev['id']." and status=1");
$sponsore = array();
$s = array();
while($row = $spon->fetch_assoc())
{
	$sponsore['sponsore_id'] = $row['id'];
	$sponsore['sponsore_img'] = $row['img'];
	$sponsore['sponsore_title'] = $row['title'];
	$s[] = $sponsore;
}
$nav['sponsore_list'] = $s[0];
$ulist = $event->query("SELECT uid,eid FROM `tbl_ticket` WHERE `eid` = ".$ev['id']." GROUP BY uid");
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
$nav['member_list'] = $member;
$ticket = $event->query("SELECT sum(`ticket_book`) as books FROM `tbl_type_price` WHERE eid=".$ev['id']."")->fetch_assoc();
$nav['total_member_list'] = $ticket['books'];
	$v[] = $nav;
	
}

if(empty($v))
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Search Data Not Get!!","SearchData"=>$v);
}
else 
{
$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Search Data Get Successfully!","SearchData"=>$v);
}

}
echo json_encode($returnArr);