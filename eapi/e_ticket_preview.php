<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$uid = $data['uid'];
$tid = $data['tid'];
if($uid == ''  or $tid == '')
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went wrong  try again !");
}
else 
{
	$datav = array();

	$ticket = $event->query("SELECT * FROM `tbl_ticket` where id=".$tid."")->fetch_assoc();
	$eve = $event->query("SELECT * FROM `tbl_event` where id=".$ticket['eid']."")->fetch_assoc();
	$user = $event->query("SELECT id,name,email,mobile,pro_pic,ccode FROM `tbl_user` where id=".$ticket['uid']."")->fetch_assoc();
	$type_ticket = $event->query("SELECT * FROM `tbl_type_price` where id=".$ticket['typeid']."")->fetch_assoc();
	

	$datav['ticket_img'] =  $eve['img'];
	$datav['ticket_title'] =  $eve['title'];
	$datav['ticket_username'] =  $user['name'];
	$date=date_create($eve['sdate']);
	$datav['event_sdate'] = date_format($date,"d F, Y");
	$datav['start_time'] = date("g:i A", strtotime($eve['stime']));
	$datav['event_address'] = $eve['address'];
	$datav['event_address_title'] = $eve['place_name'];
	$datav['event_latitude'] = $eve['latitude'];
	$datav['event_longtitude'] = $eve['longtitude'];
	$datav["userdata"]=$user;
	$datav["eventdata"]=$eve;
	$datav["ticketdata"]=$ticket;
	$datav["typeticket"]=$type_ticket;

	$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Ticket Preview Get Successfully!","TicketData"=>$datav);
}
echo json_encode($returnArr);