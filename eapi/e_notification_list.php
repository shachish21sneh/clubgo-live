<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
$data = json_decode(file_get_contents('php://input'), true);
if($data['uid'] == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
    $uid = strip_tags(mysqli_real_escape_string($event,$data['uid']));
    
    
$check = $event->query("select * from tbl_notification where uid=".$uid."");
$op = array();
while($row = $check->fetch_assoc())
{
		$op[] = $row;
}
$returnArr = array("NotificationData"=>$op,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Notification List Get Successfully!!");
}
echo json_encode($returnArr);