<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';

$data = json_decode(file_get_contents('php://input'), true);
if($data['uid'] == '')
{ 
 $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");    
}
else
{	
 $uid =  strip_tags(mysqli_real_escape_string($event,$data['uid']));
 $status = $data['status'];
 if($status == 'Booked')
 {
  $sel = $event->query("select * from tbl_ticket where uid=".$uid." and ticket_type ='Booked' order by id desc");
 }
 else if($status == 'Cancelled')
 {
	 $sel = $event->query("select * from tbl_ticket where uid=".$uid." and ticket_type ='Cancelled' order by id desc");
 }
 else 
 {
	 $sel = $event->query("select * from tbl_ticket where uid=".$uid." and ticket_type ='Completed' order by id desc");
 }
  if($sel->num_rows != 0)
  {
  $nav = array();
  $v = array();
  while($row = $sel->fetch_assoc())
    {
	$getevent = $event->query("select * from tbl_event where id=".$row['eid']."")->fetch_assoc();
	$nav['event_id'] = $getevent['id'];
	$nav['event_title'] = $getevent['title'];
	$nav['event_img'] = $getevent['img'];
	$date=date_create($getevent['sdate']);
	$nav['event_sdate'] = date_format($date,"d F");
	$nav['event_address'] = $getevent['address'];
	$nav['ticket_id'] = $row['id'];
	$nav['is_review'] = $row['is_review'];
	$v[] = $nav;
	}
   
   
      
            
    $returnArr = array("order_data"=>$v,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Ticket Get successfully!");
  }
  else 
  {
	  if($status == 'Booked')
 {
	$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"No Paid Ticket Found!");   
 }
 else if($status == 'Completed')
 {
	 $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"No Completed Ticket Found!");   
 }
 else 
 {
	 $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"No Cancelled Ticket Found!");   
 }
  }
}
echo json_encode($returnArr);