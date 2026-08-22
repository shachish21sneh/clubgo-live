<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
require dirname( dirname(__FILE__) ).'/include/eventmania.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$uid = $data['uid'];
$eid = $data['eid'];
$type = $data['type'];
$typeid = $data['typeid'];
$price = $data['price'];
$subtotal = $data['subtotal'];
$cou_amt = $data['cou_amt'];
$total_ticket = $data['total_ticket'];
$total_amt = $data['total_amt'];
$tax = $data['tax'];
$wall_amt = $data['wall_amt'];
$p_method_id = $data['p_method_id'];
$transaction_id = $data['transaction_id'];

$select_date = $data['select_date'];
$arrival_time = $data['arrival_time'];
$guests_couple = $data['guests_couple'];
$guests_male = $data['guests_male'];
$guests_female = $data['guests_female'];


if($p_method_id == '' or $transaction_id == '' or $typeid == '' or $uid == '' or $tax == '' or $eid == '' or $type == '' or $price == '' or $subtotal == '' or $cou_amt == '' or $total_ticket == '' or $total_amt == '')
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went wrong  try again !");
}
else 
{
	$vp = $event->query("select * from tbl_user where id=".$uid."")->fetch_assoc();
	 if($vp['wallet'] >= $data['wall_amt'])
	 {
	$table="tbl_ticket";
  $field_values=array("p_method_id","transaction_id","eid","type","price","subtotal","cou_amt","total_ticket","total_amt","uid","wall_amt","typeid","tax","select_date","arrival_time","guests_couple","guests_male","guests_female");
  $data_values=array("$p_method_id","$transaction_id","$eid","$type","$price","$subtotal","$cou_amt","$total_ticket","$total_amt","$uid","$wall_amt","$typeid","$tax","$select_date","$arrival_time","$guests_couple","$guests_male","$guests_female");
   $h = new Eventmania();
	  $oid = $h->eventinsertdata_Api_Id($field_values,$data_values,$table);
	  
	  $book = $event->query("select * from tbl_type_price where eid=".$eid." and id=".$typeid."")->fetch_assoc();
	  $ticket = intval($book['ticket_book']) + intval($total_ticket);
	  $table="tbl_type_price";
  $field = array('ticket_book'=>"$ticket");
  $where = "where eid=".$eid." and id=".$typeid."";
$h = new Eventmania();
	  $check = $h->eventupdateData_Api($field,$table,$where);
	  
	  $udata = $event->query("select * from tbl_user where id=".$uid."")->fetch_assoc();
$name = $udata['name'];

	   

if($wall_amt != 0)
{
	$timestamp = date("Y-m-d H:i:s");

	  $mt = intval($vp['wallet'])-intval($wall_amt);
  $table="tbl_user";
  $field = array('wallet'=>"$mt");
  $where = "where id=".$uid."";
$h = new Eventmania();
	  $check = $h->eventupdateData_Api($field,$table,$where);
	  
	  $table="wallet_report";
  $field_values=array("uid","message","status","amt","tdate");
  $data_values=array("$uid",'Wallet Used in Booking Id#'.$oid,'Debit',"$wall_amt","$timestamp");
   
      $h = new Eventmania();
	  $checks = $h->eventinsertdata_Api($field_values,$data_values,$table);
}


$content = array(
       "en" => $name.', Your Booking #'.$oid.' Has Been Received.'
   );
$heading = array(
   "en" => "Booking Received!!"
);

$fields = array(
'app_id' => $set['one_key'],
'included_segments' =>  array("Active Users"),
'data' => array("order_id" =>$oid,"type"=>'normal'),
'filters' => array(array('field' => 'tag', 'key' => 'storeid', 'relation' => '=', 'value' => $uid)),
'contents' => $content,
'headings' => $heading
);
$fields = json_encode($fields);

 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
curl_setopt($ch, CURLOPT_HTTPHEADER, 
array('Content-Type: application/json; charset=utf-8',
'Authorization: Basic '.$set['one_hash']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
 
$response = curl_exec($ch);
curl_close($ch);


$timestamp = date("Y-m-d H:i:s");

 $title_mains = "Booking Received!!";
$descriptions = 'New Booking #'.$oid.' Has Been Received.';

	   $table="tbl_notification";
  $field_values=array("uid","datetime","title","description");
  $data_values=array("$uid","$timestamp","$title_mains","$descriptions");
  
    $h = new Eventmania();
	   $h->eventinsertdata_Api($field_values,$data_values,$table);

	   $tbwallet = $event->query("select * from tbl_user where id=".$uid."")->fetch_assoc();
$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Order Placed Successfully!!!","wallet"=>$tbwallet['wallet'],"order_id" =>$oid);
	 }
	 else 
	 {
		 $tbwallet = $event->query("select * from tbl_user where id=".$uid."")->fetch_assoc();
$returnArr = array("ResponseCode"=>"200","Result"=>"false","ResponseMsg"=>"Wallet Balance Not There As Per Order Refresh One Time Screen!!!","wallet"=>$tbwallet['wallet']);
	 }
	  
}

echo json_encode($returnArr);
?>