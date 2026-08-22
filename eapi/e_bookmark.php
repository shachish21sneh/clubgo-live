<?php
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
require dirname( dirname(__FILE__) ).'/include/eventmania.php';
$data = json_decode(file_get_contents('php://input'), true);
$uid = $data['uid'];
$eid = $data['eid'];

if($uid == '' or $eid == '' )
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went wrong  try again !");
}
else 
{
 $check = $event->query("select * from tbl_fav where uid=".$uid." and eid=".$eid."")->num_rows;
 if($check != 0)
 {
      
	  
	  $table="tbl_fav";
$where = "where uid=".$uid." and eid=".$eid."";
$h = new Eventmania();
	$check = $h->eventDeleteData_Api($where,$table);
	
      $returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Event Successfully Removed In Bookmark List !!");
	  
 }
 else 
 {
     
	 
	 $table="tbl_fav";
  $field_values=array("uid","eid");
  $data_values=array("$uid","$eid");
  $h = new Eventmania();
  $check = $h->eventinsertdata_Api($field_values,$data_values,$table);
   $returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Event Successfully Saved In Bookmark List!!!");
   
    
 }
}
echo json_encode($returnArr);
?>