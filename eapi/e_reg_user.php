<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
require dirname( dirname(__FILE__) ).'/include/eventmania.php';
require dirname( dirname(__FILE__) ) . '/firebase/baseFile.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$data = json_decode(file_get_contents('php://input'), true);
function generate_random()
{
	require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
	$six_digit_random_number = mt_rand(100000, 999999);
	$c_refer = $event->query("select * from tbl_user where code=".$six_digit_random_number."")->num_rows;
	if($c_refer != 0)
	{
		generate_random();
	}
	else 
	{
		return $six_digit_random_number;
	}
}

if($data['name'] == ''  or $data['mobile'] == ''   or $data['password'] == '' or $data['ccode'] == '' or $data['email'] == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
    
    $name = strip_tags(mysqli_real_escape_string($event,$data['name']));
	$email = strip_tags(mysqli_real_escape_string($event,$data['email']));
    $mobile = strip_tags(mysqli_real_escape_string($event,$data['mobile']));
	$ccode = strip_tags(mysqli_real_escape_string($event,$data['ccode']));
     $password = md5(strip_tags(mysqli_real_escape_string($event,$data['password'])));
     $refercode = strip_tags(mysqli_real_escape_string($event,$data['refercode']));
     $device_token = strip_tags(mysqli_real_escape_string($event,$data['device_token']));
	 $device_type = strip_tags(mysqli_real_escape_string($event,$data['device_type']));
     
    $checkmob = $event->query("select * from tbl_user where mobile=".$mobile."");
    
   
    if($checkmob->num_rows != 0)
    {
        $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Mobile Number Already Used!");
    }
    else
    {
       
	   if($refercode != '')
	   {
		 $c_refer = $event->query("select * from tbl_user where code=".$refercode."")->num_rows;
		 if($c_refer != 0)
		 {
			 
        $timestamp = date("Y-m-d H:i:s");
        $prentcode = generate_random();
		$wallet = $event->query("select * from tbl_setting")->fetch_assoc();
		$fin = $wallet['scredit'];
		$table="tbl_user";
        $field_values=array("name","email","mobile","rdate","password","ccode","refercode","wallet","code");
        $data_values=array("$name","$email","$mobile","$timestamp","$password","$ccode","$refercode","$fin","$prentcode");
  
      $h = new Eventmania();
	  $check = $h->eventinsertdata_Api_Id($field_values,$data_values,$table);
	  
	  $table="wallet_report";
  $field_values=array("uid","message","status","amt","tdate");
  $data_values=array("$check",'Sign up Credit Added!!','Credit',"$fin","$timestamp");
   
      $h = new Eventmania();
	  $checks = $h->eventinsertdata_Api($field_values,$data_values,$table);
	  
      if($device_token != '' and $device_type != '')
     {
     $event->query("update tbl_user set device_token='".$device_token."', device_type='".$device_type."' where mobile='".$mobile."'");
      }



       $c = $event->query("select * from tbl_user where id=".$check."")->fetch_assoc();

	   $name=$c['name'];

        $notification = [
            'title' => 'Account Register',
            'body' => ucfirst($name) . ", Welcome in ClubGo"
        ];
		 if($device_token != '' and $device_type != '')
    {
        sendNotificationBase($device_token, $notification);
    }
       
    
        $returnArr = array("UserLogin"=>$c,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Sign Up Done Successfully!");
    }
	else 
		 {
		$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Refer Code Not Found Please Try Again!!");
	   }
	   }
	   else 
	   {
		   $timestamp = date("Y-m-d H:i:s");
		   $prentcode = generate_random();
		   $table="tbl_user";
  $field_values=array("name","email","mobile","rdate","password","ccode","code");
  $data_values=array("$name","$email","$mobile","$timestamp","$password","$ccode","$prentcode");
   $h = new Eventmania();
	  $check = $h->eventinsertdata_Api($field_values,$data_values,$table);


		 
      if($device_token != '' and $device_type != '')
     {
     $event->query("update tbl_user set device_token='".$device_token."', device_type='".$device_type."' where mobile='".$mobile."'");
      }

  $c = $event->query("select * from tbl_user where id=".$check."")->fetch_assoc();

  $name=$c['name'];

        $notification = [
            'title' => 'Account Register',
            'body' => ucfirst($name) . ", Welcome in ClubGo"
        ];

		 if($device_token != '' and $device_type != '')
    {
        sendNotificationBase($device_token, $notification);
    }
    
  $returnArr = array("UserLogin"=>$c,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Sign Up Done Successfully!");
  
	   }
    
}
}

echo json_encode($returnArr);