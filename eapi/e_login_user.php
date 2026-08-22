<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
require dirname( dirname(__FILE__) ) . '/firebase/baseFile.php';

$data = json_decode(file_get_contents('php://input'), true);
if($data['mobile'] == ''  or $data['password'] == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
    $mobile = strip_tags(mysqli_real_escape_string($event,$data['mobile']));
    $password = md5(strip_tags(mysqli_real_escape_string($event,$data['password'])));
     $device_token = strip_tags(mysqli_real_escape_string($event,$data['device_token']));
	 $device_type = strip_tags(mysqli_real_escape_string($event,$data['device_type']));
    
$chek = $event->query("select * from tbl_user where mobile='".$mobile."' and status = 1 and password='".$password."'");
$status = $event->query("select * from tbl_user where mobile='".$mobile."' and status = 1");
if($status->num_rows !=0)
{
if($chek->num_rows != 0)
{
    if($device_token != '' and $device_type != '')
    {
    $event->query("update tbl_user set device_token='".$device_token."', device_type='".$device_type."' where mobile='".$mobile."'");
    }

    $c = $event->query("select * from tbl_user where mobile='".$mobile."'  and status = 1 and password='".$password."'");
    $c = $c->fetch_assoc();
	$name=$c['name'];

        $notification = [
            'title' => 'Account Login',
            'body' => ucfirst($name) . ", Welcome in ClubGo"
        ];
         if($device_token != '' and $device_type != '')
    {
        sendNotificationBase($device_token, $notification);
    }

    $returnArr = array("UserLogin"=>$c,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Login successfully!");
}
else
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Invalid Email/Mobile No or Password!!!");
}
}
else  
{
	 $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Your Status Deactivate!!!");
}
}

echo json_encode($returnArr);