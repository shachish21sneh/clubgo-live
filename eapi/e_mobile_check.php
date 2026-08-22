<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';

$data = json_decode(file_get_contents('php://input'), true);
if($data['mobile'] == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
    $mobile = strip_tags(mysqli_real_escape_string($event,$data['mobile']));
    
    
$chek = $event->query("select * from tbl_user where mobile='".$mobile."'")->num_rows;

if($chek != 0)
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Already Exist Mobile Number!");
}
else 
{
	$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"New Number!");
}
}
echo json_encode($returnArr);