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
	$user = $event->query("SELECT * FROM `tbl_user` where id=".$ticket['uid']."")->fetch_assoc();
	$pdata = $event->query("select * from tbl_payment_list where id=".$ticket['p_method_id']."")->fetch_assoc();
	$datav['ticket_id'] = $tid;
	$datav['ticket_title'] =  $eve['title'];
	
	$date=date_create($eve['sdate']);
	$datav['start_time'] = date_format($date,"l").','.date_format($date,"M d").' - '.date("g:i A", strtotime($eve['stime'])).' - '.date("g:i A", strtotime($eve['etime']));
	$datav['event_address'] = $eve['address'];
	$datav['event_address_title'] = $eve['place_name'];
	$datav['event_latitude'] = $eve['latitude'];
	$datav['event_longtitude'] = $eve['longtitude'];
	$spon = $event->query("select * from tbl_sponsore where eid=".$ticket['eid']." and status=1");
$sponsore = array();
$s = array();
while($row = $spon->fetch_assoc())
{
	$sponsore['sponsore_id'] = $row['id'];
	$sponsore['sponsore_img'] = $row['img'];
	$sponsore['sponsore_title'] = $row['title'];
	$s[] = $sponsore;
}
$datav['event_sponsore'] = $s;

$datav['ticket_username'] =  $user['name'];
$datav['ticket_mobile'] =  $user['mobile'];
$datav['ticket_email'] =  $user['email'];

$datav['ticket_type'] = $ticket['type'];
$datav['total_ticket'] = $ticket['total_ticket'];
$datav['ticket_subtotal'] = $ticket['subtotal'];
$datav['ticket_cou_amt'] = $ticket['cou_amt'];
$datav['ticket_wall_amt'] = $ticket['wall_amt'];
$datav['ticket_tax'] = $ticket['tax'];
$datav['ticket_total_amt'] = $ticket['total_amt'];

$datav['ticket_p_method'] = $pdata['title'];
$datav['ticket_transaction_id'] = $ticket['transaction_id'];
if($ticket['ticket_type'] == 'Cancelled')
{
	$datav['ticket_status'] = 'Cancelled';
}
else 
{
$datav['ticket_status'] = 'Paid';
}
	$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Ticket Information Get Successfully!","TicketData"=>$datav);
}
echo json_encode($returnArr);