<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
require dirname( dirname(__FILE__) ).'/include/eventmania.php';
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
if($data['uid'] == '' or $data['tid'] == '')
{
 $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");    
}
else
{
	 $tid = $event->real_escape_string($data['tid']);
 $uid =  $event->real_escape_string($data['uid']);
 $cancle_comment = $event->real_escape_string($data['cancle_comment']);
 
 $check_status = $event->query("select * from tbl_ticket where uid=".$uid." and id=".$tid."")->fetch_assoc();
 if($check_status['ticket_type'] == 'Cancelled')
 {
	 $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Already Cancelled Ticket!");    
 }
 else 
 {
 $table="tbl_ticket";
  $field = array('ticket_type'=>'Cancelled','cancle_comment'=>"$cancle_comment");
  $where = "where uid=".$uid." and id=".$tid."";
$h = new Eventmania();
	  $check = $h->eventupdateData_Api($field,$table,$where);
	  
	  $getticket = $event->query("select * from tbl_ticket where uid=".$uid." and id=".$tid."")->fetch_assoc();
	  $eid = $getticket['eid'];
	  $typeid = $getticket['typeid'];
	  $book = $event->query("select * from tbl_type_price where eid=".$eid." and id=".$typeid."")->fetch_assoc();
	  $ticket = intval($book['ticket_book']) - intval($getticket['total_ticket']);
	  $table="tbl_type_price";
  $field = array('ticket_book'=>"$ticket");
  $where = "where eid=".$eid." and id=".$typeid."";
$h = new Eventmania();
	  $check = $h->eventupdateData_Api($field,$table,$where);
	  
 $returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Ticket  Cancelled successfully!");
 }
}
echo json_encode($returnArr);
?>