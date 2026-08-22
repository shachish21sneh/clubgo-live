<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';

//header('Content-type: text/json');
header('Content-Type: application/json; charset=utf-8');
$sel = $event->query("SELECT * 
FROM tbl_code
ORDER BY 
  CASE 
    WHEN ccode = '+91' THEN 0
    ELSE 1
  END,
  CAST(REPLACE(ccode, '+', '') AS UNSIGNED)");
$myarray = array();
while($row = $sel->fetch_assoc())
{
			$myarray[] = $row;
}
$returnArr = array("CountryCode"=>$myarray,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Country Code List Founded!");
echo json_encode($returnArr);
?>