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
 $total_star = $event->real_escape_string($data['total_star']);
 $review_comment = $event->real_escape_string($data['review_comment']);
 
 $check_status = $event->query("select * from tbl_ticket where uid=".$uid." and id=".$tid."")->fetch_assoc();
 
 $table="tbl_ticket";
  $field = array('total_star'=>"$total_star",'review_comment'=>"$review_comment",'is_review'=>'1');
  $where = "where uid=".$uid." and id=".$tid."";
$h = new Eventmania();
	  $check = $h->eventupdateData_Api($field,$table,$where);
	  
 $returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Ticket  Review Done successfully!");
}
echo json_encode($returnArr);
?>