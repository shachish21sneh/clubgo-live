<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$uid = $data['uid'];

if($uid == '')
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went wrong  try again !");
}
else 
{
	$v = array();
	
	

$counter = $event->query("select * from tbl_fav where uid=".$uid."");
   
	if($counter->num_rows != 0)
    {
		
		$pop = array();
		$pol = array();
		while($folk = $counter->fetch_assoc())
		{
$eventlist = $event->query("select * from tbl_event where status=1 and id=".$folk['eid']."");
$nav = array();
while($ev = $eventlist->fetch_assoc())
{
	$nav['event_id'] = $ev['id'];
	$nav['event_title'] = $ev['title'];
	$nav['event_img'] = $ev['img'];
	$date=date_create($ev['sdate']);
	$nav['event_sdate'] = date_format($date,"d F");
	$nav['event_address'] = $ev['address'];
	$v[] = $nav;
}
		}
	
	$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Bookmark List Get Successfully!","EventData"=>$v);
	}
else 
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Event  Bookmark List Not Found!");
}	

}
echo json_encode($returnArr);