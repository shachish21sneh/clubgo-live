<?php
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
require dirname( dirname(__FILE__) ).'/include/eventmania.php';
$data = json_decode(file_get_contents('php://input'), true);
$uid = $data['uid'];
$vid = $data['vid'];

if($uid == '' or $vid == '' )
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went wrong  try again !");
}
else 
{
 $check = $event->query("select * from tbl_fav_venue where uid=".$uid." and vid=".$vid."")->num_rows;
 if($check != 0)
 {
      
	  
	  $table="tbl_fav_venue";
$where = "where uid=".$uid." and vid=".$vid."";
$h = new Eventmania();
	$check = $h->eventDeleteData_Api($where,$table);
	
      $returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Venue Successfully Removed In Bookmark List !!");
	  
 }
 else 
 {
     
	 
	 $table="tbl_fav_venue";
  $field_values=array("uid","vid");
  $data_values=array("$uid","$vid");
  $h = new Eventmania();
  $check = $h->eventinsertdata_Api($field_values,$data_values,$table);
   $returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Venue Successfully Saved In Bookmark List!!!");
   
    
 }
}
echo json_encode($returnArr);
?>